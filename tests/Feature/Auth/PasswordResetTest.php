<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200)
            ->assertSeeText('Minta tautan pemulihan')
            ->assertSee('immanuel-production-white-logo.png', false);
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPasswordNotification::class, function ($notification) use ($user) {
            $response = $this->get('/reset-password/'.$notification->token.'?email='.urlencode($user->email));

            $response->assertStatus(200)
                ->assertSeeText('Masukkan password baru')
                ->assertSee($user->email);

            return true;
        });
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPasswordNotification::class, function ($notification) use ($user) {
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $response
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('login'));

            return true;
        });
    }

    public function test_reset_password_email_uses_branded_template_and_secure_link(): void
    {
        Notification::fake();

        $user = User::factory()->create(['name' => 'Immanuel User']);
        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPasswordNotification::class, function ($notification) use ($user) {
            $message = $notification->toMail($user);

            $this->assertSame('Atur Ulang Password | Portal Immanuel Production', $message->subject);
            $this->assertSame('emails.auth.reset-password', $message->view['html']);
            $this->assertSame('emails.auth.reset-password-text', $message->view['text']);
            $this->assertSame('Immanuel User', $message->viewData['recipientName']);
            $this->assertSame(60, $message->viewData['expiresIn']);
            $this->assertStringContainsString('/reset-password/', $message->viewData['resetUrl']);
            $this->assertStringContainsString(urlencode($user->email), $message->viewData['resetUrl']);

            return true;
        });
    }
}
