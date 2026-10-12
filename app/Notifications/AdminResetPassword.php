<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class AdminResetPassword extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = url(route('admin.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Reset your '.config('app.name').' admin password')
            ->greeting('Hi '.($notifiable->name ?: 'there').',')
            ->line('A password reset was requested for your admin account.')
            ->action('Reset password', $url)
            ->line('This link expires in '.config('auth.passwords.admins.expire').' minutes.')
            ->line('If you didn\'t request this, please ignore — no changes will be made.');
    }
}
