<?php

namespace App\Http\Controllers;

use App\Models\FieldJob;
use App\Models\FieldJobPhoto;
use App\Models\FieldJobStage;
use App\Models\User;
use App\Services\AuditTrail;
use App\Services\PrivateImageStorage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FieldJobController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $this->authorize('fieldjobsmenu');
        $search = trim((string) $request->input('search'));
        $status = (string) $request->input('status');

        $jobs = FieldJob::query()
            ->visibleTo($request->user())
            ->with(['activeStages' => fn ($query) => $query
                ->with('assignees:id,name')->orderBy('scheduled_at')])
            ->when($search, fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('job_number', 'like', "%{$search}%")
                    ->orWhere('client_name', 'like', "%{$search}%")
                    ->orWhere('event_name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            }))
            ->when(in_array($status, [
                FieldJob::STATUS_PENDING, FieldJob::STATUS_IN_PROGRESS,
                FieldJob::STATUS_COMPLETED, FieldJob::STATUS_CANCELLED,
            ], true), fn ($query) => $query->where('status', $status))
            ->orderByRaw('COALESCE(loading_date, event_date) IS NULL')
            ->orderBy('loading_date')
            ->latest('id')
            ->paginate(min(max((int) $request->input('per_page', 12), 6), 60))
            ->withQueryString();

        return view('field-jobs.index', compact('jobs', 'search', 'status'));
    }

    public function show(Request $request, FieldJob $fieldJob): View
    {
        $this->authorize('fieldjobsmenu');
        $this->ensureCanView($request->user(), $fieldJob);

        $fieldJob->load([
            'items',
            'stages' => fn ($query) => $query->where('is_active', true)
                ->with(['assignees:id,name', 'photos.uploader:id,name', 'starter:id,name', 'completer:id,name'])
                ->orderByRaw("CASE type WHEN 'install' THEN 1 WHEN 'one_way' THEN 2 ELSE 3 END"),
        ]);

        if ($request->user()->can('invoicemenu')) {
            $fieldJob->load('invoice:id,invoice_number');
        }

        $teamMembers = collect();
        if ($request->user()->canManageAllFieldJobs()) {
            $teamMembers = User::query()->where('active', true)->with('role:id,name')
                ->orderBy('name')->get(['id', 'name', 'role_id']);
        }

        return view('field-jobs.show', compact('fieldJob', 'teamMembers'));
    }

    public function updateAssignments(Request $request, FieldJob $fieldJob, FieldJobStage $stage)
    {
        $this->authorize('managefieldjobs');
        abort_unless($request->user()->canManageAllFieldJobs(), 403);
        $this->ensureStageBelongsToJob($fieldJob, $stage);
        abort_unless($stage->is_active, 422, 'Tahap pekerjaan ini sudah tidak aktif.');

        $data = $request->validate([
            'assignee_ids' => ['nullable', 'array', 'max:50'],
            'assignee_ids.*' => [
                'integer', 'distinct',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('active', true)),
            ],
            'copy_to_teardown' => ['nullable', 'boolean'],
        ]);
        $ids = collect($data['assignee_ids'] ?? [])->map(fn ($id) => (int) $id)->unique()->values();
        $sync = $ids->mapWithKeys(fn (int $id) => [$id => ['assigned_by' => $request->user()->id]])->all();
        $before = $stage->assignees()->pluck('users.id')->all();
        $stage->assignees()->sync($sync);

        if ($stage->type === FieldJobStage::TYPE_INSTALL && $request->boolean('copy_to_teardown')) {
            $teardown = $fieldJob->stages()->where('type', FieldJobStage::TYPE_TEARDOWN)
                ->where('is_active', true)->first();
            $teardown?->assignees()->sync($sync);
        }

        AuditTrail::record('field_job.assignments_updated', $stage, ['assignee_ids' => $before], ['assignee_ids' => $ids->all()]);

        return back()->with('success', 'Tim '.$stage->label().' berhasil diperbarui.');
    }

    public function updateStage(Request $request, FieldJob $fieldJob, FieldJobStage $stage)
    {
        $this->authorize('updatefieldjobstatus');
        $this->ensureStageBelongsToJob($fieldJob, $stage);
        $this->ensureCanActOnStage($request->user(), $stage);
        abort_unless($stage->is_active, 422, 'Tahap pekerjaan ini sudah tidak aktif.');

        $data = $request->validate([
            'status' => ['required', Rule::in([
                FieldJobStage::STATUS_PENDING,
                FieldJobStage::STATUS_IN_PROGRESS,
                FieldJobStage::STATUS_COMPLETED,
            ])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($data['status'] === FieldJobStage::STATUS_PENDING && ! $request->user()->canManageAllFieldJobs()) {
            abort(403, 'Hanya Master, Admin, atau Mandor yang dapat membuka kembali pekerjaan.');
        }
        if (
            $data['status'] === FieldJobStage::STATUS_COMPLETED
            && $stage->type !== FieldJobStage::TYPE_TEARDOWN
            && ! $stage->photos()->exists()
        ) {
            return back()->with('error', 'Unggah minimal satu foto hasil sebelum tahap ditandai selesai.');
        }

        $before = $stage->toArray();
        $attributes = ['status' => $data['status'], 'notes' => $data['notes'] ?? $stage->notes];
        if ($data['status'] === FieldJobStage::STATUS_IN_PROGRESS) {
            $attributes += ['started_at' => $stage->started_at ?: now(), 'started_by' => $stage->started_by ?: $request->user()->id];
            $attributes += ['completed_at' => null, 'completed_by' => null];
        } elseif ($data['status'] === FieldJobStage::STATUS_COMPLETED) {
            $attributes += [
                'started_at' => $stage->started_at ?: now(),
                'started_by' => $stage->started_by ?: $request->user()->id,
                'completed_at' => now(),
                'completed_by' => $request->user()->id,
            ];
        } else {
            $attributes += ['started_at' => null, 'started_by' => null, 'completed_at' => null, 'completed_by' => null];
        }

        $stage->update($attributes);
        $fieldJob->refresh()->recalculateStatus();
        AuditTrail::record('field_job.stage_updated', $stage, $before, $stage->fresh()->toArray());

        return back()->with('success', 'Status '.$stage->label().' berhasil diperbarui.');
    }

    public function storePhotos(
        Request $request,
        FieldJob $fieldJob,
        FieldJobStage $stage,
        PrivateImageStorage $imageStorage,
    ) {
        $this->authorize('uploadfieldjobphotos');
        $this->ensureStageBelongsToJob($fieldJob, $stage);
        $this->ensureCanActOnStage($request->user(), $stage);
        abort_unless($stage->is_active, 422, 'Tahap pekerjaan ini sudah tidak aktif.');

        $data = $request->validate([
            'photos' => ['required', 'array', 'min:1'],
            'photos.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192', 'dimensions:max_width=12000,max_height=12000'],
            'caption' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($request->file('photos', []) as $file) {
            $stored = $imageStorage->store($file, "field-jobs/{$fieldJob->id}/{$stage->type}");
            $photo = $stage->photos()->create($stored + [
                'original_name' => $file->getClientOriginalName(),
                'caption' => $data['caption'] ?? null,
                'uploaded_by' => $request->user()->id,
            ]);
            AuditTrail::record('field_job.photo_uploaded', $photo, [], ['stage_id' => $stage->id]);
        }

        if ($stage->status === FieldJobStage::STATUS_PENDING) {
            $stage->update([
                'status' => FieldJobStage::STATUS_IN_PROGRESS,
                'started_at' => now(),
                'started_by' => $request->user()->id,
            ]);
            $fieldJob->refresh()->recalculateStatus();
        }

        return back()->with('success', count($request->file('photos', [])).' foto berhasil diunggah dan disimpan privat.');
    }

    public function photo(Request $request, FieldJob $fieldJob, FieldJobStage $stage, FieldJobPhoto $photo)
    {
        $this->authorize('fieldjobsmenu');
        $this->ensureCanView($request->user(), $fieldJob);
        $this->ensureStageBelongsToJob($fieldJob, $stage);
        abort_unless((int) $photo->field_job_stage_id === (int) $stage->id, 404);
        abort_unless(Storage::disk('local')->exists($photo->path), 404);

        return Storage::disk('local')->response($photo->path, $photo->original_name, [
            'Content-Type' => $photo->mime_type,
            'Cache-Control' => 'private, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function destroyPhoto(Request $request, FieldJob $fieldJob, FieldJobStage $stage, FieldJobPhoto $photo)
    {
        $this->authorize('uploadfieldjobphotos');
        $this->ensureStageBelongsToJob($fieldJob, $stage);
        abort_unless((int) $photo->field_job_stage_id === (int) $stage->id, 404);
        $isManager = $request->user()->canManageAllFieldJobs();
        abort_unless($isManager || ((int) $photo->uploaded_by === (int) $request->user()->id && $this->isAssigned($request->user(), $stage)), 403);

        if (
            $stage->status === FieldJobStage::STATUS_COMPLETED
            && $stage->type !== FieldJobStage::TYPE_TEARDOWN
            && $stage->photos()->count() <= 1
        ) {
            return back()->with('error', 'Buka kembali tahap pekerjaan sebelum menghapus satu-satunya foto hasil.');
        }

        Storage::disk('local')->delete($photo->path);
        AuditTrail::record('field_job.photo_deleted', $photo, $photo->toArray());
        $photo->delete();

        return back()->with('success', 'Foto berhasil dihapus.');
    }

    private function ensureCanView(User $user, FieldJob $fieldJob): void
    {
        if ($user->canViewAllFieldJobs()) {
            return;
        }

        abort_unless($fieldJob->stages()->where('is_active', true)
            ->whereHas('assignees', fn ($query) => $query->whereKey($user->id))->exists(), 403);
    }

    private function ensureCanActOnStage(User $user, FieldJobStage $stage): void
    {
        abort_unless($user->canManageAllFieldJobs() || $this->isAssigned($user, $stage), 403);
    }

    private function isAssigned(User $user, FieldJobStage $stage): bool
    {
        return $stage->assignees()->whereKey($user->id)->exists();
    }

    private function ensureStageBelongsToJob(FieldJob $fieldJob, FieldJobStage $stage): void
    {
        abort_unless((int) $stage->field_job_id === (int) $fieldJob->id, 404);
    }
}
