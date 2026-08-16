<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// ⟵ tambahkan ini
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        // === NOTIF LOGIN (allow-list) ===
        try {
            $user = $request->user();
            if ($user) {
                $payload = [
                    'title' => 'User login',
                    'message' => "{$user->name} telah login",
                    'link' => route('dashboard'),
                    'icon' => 'log-in',
                ];
                // broadcast ke semua role yang diizinkan untuk tipe ini
                NotificationService::pushToAllowedRoles('user_logged_in', $payload);
            }
        } catch (\Throwable $e) {
            // diamkan / boleh log kalau perlu
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // simpan user sebelum logout agar masih bisa dipakai di payload
        $user = $request->user();

        // === NOTIF LOGOUT (allow-list) ===
        try {
            if ($user) {
                $payload = [
                    'title' => 'User logout',
                    'message' => "{$user->name} telah logout",
                    'link' => route('dashboard'),
                    'icon' => 'log-out',
                ];
                NotificationService::pushToAllowedRoles('user_logged_out', $payload);
            }
        } catch (\Throwable $e) {
            // diamkan / boleh log kalau perlu
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
