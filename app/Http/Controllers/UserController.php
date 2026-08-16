<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Services\AuditTrail;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

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
                ->orWhere('email', 'like', "%{$search}%")))
            ->orderBy('name')->paginate(20)->withQueryString();

        return view('users.index', compact('users', 'search'));
    }

    public function create()
    {
        $this->authorize('createuser');

        return view('users.create', ['roles' => Role::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $this->authorize('createuser');
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'no_telf' => ['nullable', 'string'],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['required', 'confirmed', Password::min(10)->letters()->numbers()],
        ]);
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

    public function update(Request $request, User $user)
    {
        $this->authorize('edituser');
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'no_telf' => ['nullable', 'string'],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['nullable', 'confirmed', Password::min(10)->letters()->numbers()],
        ]);
        if (empty($data['password'])) {
            unset($data['password']);
        }
        $before = $user->only(['name', 'email', 'no_telf', 'role_id', 'active']);
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
        $user->delete();
        AuditTrail::record('user.deleted', $user);

        return redirect()->route('users.index')->with('success', 'Akun dihapus.');
    }
}
