<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $expiresIn = (int) config(
            'auth.passwords.'.config('auth.defaults.passwords').'.expire',
            60
        );

        $data = [
            'recipientName' => $notifiable->name ?: 'Pengguna',
            'resetUrl' => $this->resetUrl($notifiable),
            'expiresIn' => $expiresIn,
        ];

        return (new MailMessage)
            ->subject('Atur Ulang Password | Portal Immanuel Production')
            ->view('emails.auth.reset-password', $data)
            ->text('emails.auth.reset-password-text', $data);
    }
}
