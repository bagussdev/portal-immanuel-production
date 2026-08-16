<?php

namespace App\Http\Controllers;

use App\Models\Armada;
use App\Models\ArmadaStnkRenewal;
use App\Models\Gudang;
use App\Models\User;
use App\Services\AuditTrail;
use App\Services\DocumentTotals;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ArmadaController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('armadamenu');
        $search = trim((string) $request->input('search'));
        $documentStatus = $request->input('document_status');
        $query = Armada::with(['location', 'user'])
            ->when($search, fn ($q) => $q->where(fn ($qq) => $qq
                ->where('name', 'like', "%{$search}%")
                ->orWhere('nomor_polisi', 'like', "%{$search}%")
                ->orWhere('brand', 'like', "%{$search}%")));

        if ($documentStatus === 'overdue') {
            $query->whereDate('stnk_expired', '<', today());
        }
        if ($documentStatus === 'due_soon') {
            $query->whereBetween('stnk_expired', [today(), today()->addDays(30)]);
        }
        if ($documentStatus === 'safe') {
            $query->whereDate('stnk_expired', '>', today()->addDays(30));
        }

        $armadas = $query->latest()->paginate(12)->withQueryString();
        $stats = [
            'overdue' => Armada::whereDate('stnk_expired', '<', today())->count(),
            'due_soon' => Armada::whereBetween('stnk_expired', [today(), today()->addDays(30)])->count(),
            'safe' => Armada::whereDate('stnk_expired', '>', today()->addDays(30))->count(),
        ];

        return view('armada.index', compact('armadas', 'search', 'documentStatus', 'stats'));
    }

    public function changes()
    {
        $this->authorize('armadamenu');

        return response()->json(['latest' => Armada::max('updated_at')]);
    }

    public function rows(Request $request)
    {
        $this->authorize('armadamenu');
        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])));
        $armadas = Armada::with(['location', 'user'])->whereIn('id', $ids)->latest()->get();

        return view('armada._rows', compact('armadas'));
    }

    public function create()
    {
        $this->authorize('createarmada');

        return view('armada.create', ['locations' => Gudang::orderBy('name')->get(), 'users' => User::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $this->authorize('createarmada');
        $data = $this->validated($request);
        $data += ['status' => Armada::STATUS_AVAILABLE];
        $this->storeUploads($request, $data);
        $armada = Armada::create($data);
        AuditTrail::record('armada.created', $armada, [], $armada->toArray());

        return redirect()->route('armada.show', $armada)->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    public function edit(Armada $armada)
    {
        $this->authorize('editarmada');

        return view('armada.edit', [
            'armada' => $armada, 'locations' => Gudang::orderBy('name')->get(),
            'users' => User::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Armada $armada)
    {
        $this->authorize('editarmada');
        $data = $this->validated($request, $armada);
        $before = $armada->toArray();
        $this->storeUploads($request, $data, $armada);
        $armada->update($data);
        AuditTrail::record('armada.updated', $armada, $before, $armada->fresh()->toArray());

        return redirect()->route('armada.show', $armada)->with('success', 'Data kendaraan diperbarui.');
    }

    public function show(Armada $armada)
    {
        $this->authorize('armadamenu');
        $armada->load(['location', 'user', 'renewals.creator']);

        return view('armada.show', compact('armada'));
    }

    public function processSamsat(Request $request, Armada $armada)
    {
        $this->authorize('samsatarmada');
        $data = $request->validate([
            'processed_at' => ['required', 'date'],
            'new_expired_at' => ['required', 'date', 'after:processed_at'],
            'cost' => ['nullable', 'string', 'max:30'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($request, $armada, $data) {
            $path = $request->file('attachment')?->store('stnk-documents', 'local');
            $renewal = $armada->renewals()->create([
                'processed_at' => $data['processed_at'],
                'previous_expired_at' => $armada->stnk_expired,
                'new_expired_at' => $data['new_expired_at'],
                'cost' => DocumentTotals::money($data['cost'] ?? null),
                'attachment' => $path,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);
            $before = $armada->only(['stnk_expired', 'stnk_renewed_at', 'stnk_attachment']);
            $armada->update([
                'stnk_renewed_at' => $data['processed_at'],
                'stnk_expired' => $data['new_expired_at'],
                'stnk_attachment' => $path ?: $armada->stnk_attachment,
            ]);
            AuditTrail::record('armada.stnk_renewed', $renewal, $before, $armada->only(array_keys($before)));
        });

        return redirect()->route('armada.show', $armada)->with('success', 'Perpanjangan STNK dicatat dan riwayat disimpan.');
    }

    public function stnkAttachment(Armada $armada)
    {
        $this->authorize('armadamenu');
        abort_unless($armada->stnk_attachment && Storage::disk('local')->exists($armada->stnk_attachment), 404);

        return Storage::disk('local')->download($armada->stnk_attachment);
    }

    public function renewalAttachment(Armada $armada, ArmadaStnkRenewal $renewal)
    {
        $this->authorize('armadamenu');
        abort_unless((int) $renewal->armada_id === (int) $armada->id && $renewal->attachment, 404);
        abort_unless(Storage::disk('local')->exists($renewal->attachment), 404);

        return Storage::disk('local')->download($renewal->attachment);
    }

    public function destroy(Armada $armada)
    {
        $this->authorize('deletearmada');
        $armada->delete();
        AuditTrail::record('armada.archived', $armada);

        return redirect()->route('armada.index')->with('success', 'Kendaraan dipindahkan ke arsip.');
    }

    private function validated(Request $request, ?Armada $armada = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'type' => ['required', 'string', 'max:80'],
            'model' => ['required', 'string', 'max:100'],
            'brand' => ['required', 'string', 'max:100'],
            'year' => ['required', 'integer', 'min:1950', 'max:'.(now()->year + 1)],
            'nomor_rangka' => ['required', 'string', 'max:100', Rule::unique('armada')->ignore($armada?->id)],
            'nomor_mesin' => ['required', 'string', 'max:100', Rule::unique('armada')->ignore($armada?->id)],
            'nomor_polisi' => ['required', 'string', 'max:24', Rule::unique('armada')->ignore($armada?->id)],
            'qr_pertamina' => ['nullable', 'image', 'max:3072'],
            'foto_depan' => ['nullable', 'image', 'max:3072'],
            'foto_belakang' => ['nullable', 'image', 'max:3072'],
            'foto_samping' => ['nullable', 'image', 'max:3072'],
            'stnk_expired' => ['nullable', 'date'],
            'stnk_attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'location_id' => ['required', 'exists:gudang,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'status' => ['nullable', Rule::in([Armada::STATUS_AVAILABLE, Armada::STATUS_IN_USE, Armada::STATUS_MAINTENANCE, Armada::STATUS_DAMAGED])],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    private function storeUploads(Request $request, array &$data, ?Armada $armada = null): void
    {
        foreach (['qr_pertamina', 'foto_depan', 'foto_belakang', 'foto_samping'] as $field) {
            if (! $request->hasFile($field)) {
                continue;
            }
            if ($armada?->{$field}) {
                Storage::disk('public')->delete($armada->{$field});
            }
            $data[$field] = $request->file($field)->store("armada/{$field}", 'public');
        }
        if ($request->hasFile('stnk_attachment')) {
            $data['stnk_attachment'] = $request->file('stnk_attachment')->store('stnk-documents', 'local');
        }
    }
}
