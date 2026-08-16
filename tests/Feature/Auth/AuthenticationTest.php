<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200)
            ->assertSeeText('Masuk ke Portal Immanuel')
            ->assertSeeText('Portal Immanuel Production')
            ->assertSee('assets/brand/immanuel-production-white-logo.png', false)
            ->assertDontSee('assets/brand/immanuel-production-legacy-logo.png', false)
            ->assertDontSee('assets/logo.png', false);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $response = $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/login')->assertSessionHasErrors([
            'email' => 'Email atau password tidak cocok. Periksa kembali lalu coba lagi.',
        ]);
    }

    public function test_inactive_users_cannot_authenticate(): void
    {
        $user = User::factory()->create(['active' => false]);

        $response = $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/login')->assertSessionHasErrors([
            'email' => 'Akun tidak aktif. Hubungi Master.',
        ]);
    }

    public function test_existing_session_is_ended_when_account_is_deactivated(): void
    {
        $user = User::factory()->create(['active' => true]);
        $this->actingAs($user);
        $user->update(['active' => false]);

        $this->get('/dashboard')
            ->assertRedirect(route('login'))
            ->assertSessionHas('error', 'Akun dinonaktifkan. Hubungi Master.');

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
