<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        try {
            $user = $request->user();
            if ($user) {
                $payload = [
                    'title' => 'User login',
                    'message' => "{$user->name} telah login",
                    'link' => route('dashboard'),
                    'icon' => 'log-in',
                ];
                NotificationService::pushToAllowedRoles('user_logged_in', $payload);
            }
        } catch (\Throwable $e) {
            // Notifikasi tidak boleh menggagalkan proses login.
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

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
            // Notifikasi tidak boleh menggagalkan proses logout.
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
