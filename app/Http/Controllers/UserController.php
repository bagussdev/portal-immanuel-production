<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Services\AuditTrail;
use App\Services\PrivateImageStorage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('menuuser');
        $search = trim((string) $request->input('search'));
        $users = User::with('role')
            ->when($search, fn ($q) => $q->where(fn ($qq) => $qq
                ->where('name', 'like', "%{$search}%")
                ->orWhere('username', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")))
            ->orderBy('name')->paginate(20)->withQueryString();

        return view('users.index', compact('users', 'search'));
    }

    public function create()
    {
        $this->authorize('createuser');

        return view('users.create', ['roles' => Role::orderBy('name')->get()]);
    }

    public function store(Request $request, PrivateImageStorage $images)
    {
        $this->authorize('createuser');
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'min:3', 'max:40', 'regex:/^[a-zA-Z0-9._-]+$/', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'no_telf' => ['nullable', 'string'],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['required', 'confirmed', Password::min(10)->letters()->numbers()],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'ktp_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
            'ktp_rotation' => ['nullable', 'integer', Rule::in([0, 90, 180, 270])],
        ]);
        $data['username'] = $this->availableUsername($data['username'] ?? null, $data['email']);
        try {
            if ($request->hasFile('profile_photo')) {
                $data['profile_photo_path'] = $images->store($request->file('profile_photo'), 'users/profile')['path'];
            }
            if ($request->hasFile('ktp_photo')) {
                $data['ktp_photo_path'] = $images->store($request->file('ktp_photo'), 'users/ktp', (int) ($data['ktp_rotation'] ?? 0))['path'];
            }
        } catch (\RuntimeException) {
            throw ValidationException::withMessages(['ktp_photo' => 'Foto tidak dapat diproses. Coba unggah ulang dengan format JPG, PNG, atau WebP.']);
        }
        unset($data['profile_photo'], $data['ktp_photo'], $data['ktp_rotation']);
        $user = User::create($data + ['active' => true]);
        AuditTrail::record('user.created', $user, [], $user->only(['name', 'email', 'role_id', 'active']));

        return redirect()->route('users.index')->with('success', 'Akun berhasil dibuat.');
    }

    public function show(User $user)
    {
        $this->authorize('menuuser');

        return redirect()->route('users.edit', $user);
    }

    public function edit(User $user)
    {
        $this->authorize('edituser');

        return view('users.edit', ['user' => $user, 'roles' => Role::orderBy('name')->get()]);
    }

    public function update(Request $request, User $user, PrivateImageStorage $images)
    {
        $this->authorize('edituser');
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'min:3', 'max:40', 'regex:/^[a-zA-Z0-9._-]+$/', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'no_telf' => ['nullable', 'string'],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['nullable', 'confirmed', Password::min(10)->letters()->numbers()],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'ktp_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
            'remove_profile_photo' => ['nullable', 'boolean'],
            'remove_ktp_photo' => ['nullable', 'boolean'],
            'ktp_rotation' => ['nullable', 'integer', Rule::in([0, 90, 180, 270])],
        ]);
        $data['username'] = filled($data['username'] ?? null)
            ? strtolower($data['username'])
            : ($user->username ?: $this->availableUsername(null, $data['email'], $user->id));
        if (empty($data['password'])) {
            unset($data['password']);
        }
        try {
            foreach ([['profile_photo', 'profile_photo_path', 'remove_profile_photo'], ['ktp_photo', 'ktp_photo_path', 'remove_ktp_photo']] as [$input, $column, $remove]) {
                if ($request->hasFile($input)) {
                    $rotation = $input === 'ktp_photo' ? (int) ($data['ktp_rotation'] ?? 0) : 0;
                    $newPath = $images->store($request->file($input), $column === 'ktp_photo_path' ? 'users/ktp' : 'users/profile', $rotation)['path'];
                    if ($user->{$column}) {
                        Storage::disk('local')->delete($user->{$column});
                    }
                    $data[$column] = $newPath;
                } elseif ($request->boolean($remove)) {
                    if ($user->{$column}) {
                        Storage::disk('local')->delete($user->{$column});
                    }
                    $data[$column] = null;
                } elseif ($input === 'ktp_photo' && (int) ($data['ktp_rotation'] ?? 0) !== 0 && $user->ktp_photo_path) {
                    if (! $images->rotateStored($user->ktp_photo_path, (int) $data['ktp_rotation'])) {
                        throw new \RuntimeException('KTP tidak dapat diputar.');
                    }
                }
                unset($data[$input], $data[$remove]);
            }
        } catch (\RuntimeException) {
            throw ValidationException::withMessages(['ktp_photo' => 'Foto tidak dapat diproses. Coba unggah ulang dengan format JPG, PNG, atau WebP.']);
        }
        unset($data['ktp_rotation']);
        $before = $user->only(['name', 'username', 'email', 'no_telf', 'role_id', 'active']);
        $user->update($data);
        AuditTrail::record('user.updated', $user, $before, $user->only(array_keys($before)));

        return redirect()->route('users.index')->with('success', 'Akun berhasil diperbarui.');
    }

    public function activate(User $user)
    {
        $this->authorize('usercontrol');
        $user->update(['active' => true]);
        AuditTrail::record('user.activated', $user);

        return back()->with('success', 'Akun diaktifkan.');
    }

    public function deactivate(User $user)
    {
        $this->authorize('usercontrol');
        abort_if($user->is(auth()->user()), 422, 'Tidak dapat menonaktifkan akun sendiri.');
        $user->update(['active' => false]);
        AuditTrail::record('user.deactivated', $user);

        return back()->with('success', 'Akun dinonaktifkan.');
    }

    public function destroy(User $user)
    {
        $this->authorize('usercontrol');
        abort_if($user->is(auth()->user()) || $user->isMaster(), 422, 'Akun ini tidak dapat dihapus.');
        foreach ([$user->profile_photo_path, $user->ktp_photo_path] as $path) {
            if ($path) {
                Storage::disk('local')->delete($path);
            }
        }
        $user->delete();
        AuditTrail::record('user.deleted', $user);

        return redirect()->route('users.index')->with('success', 'Akun dihapus.');
    }

    public function photo(User $user, string $kind)
    {
        if ($kind === 'ktp') {
            abort_unless(auth()->user()->can('exportuserdata') || auth()->user()->can('edituser'), 403);
        } else {
            abort_unless(auth()->id() === $user->id || auth()->user()->can('menuuser'), 403);
        }
        $path = $kind === 'ktp' ? $user->ktp_photo_path : $user->profile_photo_path;
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path, null, ['Cache-Control' => 'private, max-age=3600', 'X-Content-Type-Options' => 'nosniff']);
    }

    public function exportPdf()
    {
        $this->authorize('exportuserdata');
        $users = User::with('role')->where('active', true)->orderBy('name')->get();
        AuditTrail::record('users.exported_pdf', auth()->user(), [], ['active_users' => $users->count()]);

        return Pdf::loadView('users.pdf', compact('users'))->setPaper('a4')->stream('Data Crew Immanuel Production '.today()->format('d-m-Y').'.pdf');
    }

    private function availableUsername(?string $username, string $email, ?int $ignoreId = null): string
    {
        $base = strtolower(trim((string) $username));
        if ($base === '') {
            $base = Str::of(Str::before($email, '@'))->lower()->replaceMatches('/[^a-z0-9._-]+/', '.')->trim('.-_')->value();
        }
        $base = Str::limit($base ?: 'user', 34, '');
        $candidate = $base;
        $suffix = 1;
        while (User::query()->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))->where('username', $candidate)->exists()) {
            $candidate = $base.'.'.$suffix++;
        }

        return $candidate;
    }
}
