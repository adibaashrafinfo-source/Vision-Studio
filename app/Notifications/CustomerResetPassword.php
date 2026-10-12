<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class CustomerResetPassword extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = url(route('customer.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Reset your '.config('app.name').' password')
            ->greeting('Hi '.($notifiable->name ?: 'there').',')
            ->line('We received a request to reset the password for your account.')
            ->action('Reset password', $url)
            ->line('This link expires in '.config('auth.passwords.customers.expire').' minutes.')
            ->line('If you didn\'t request this, you can safely ignore this email — nothing will change.');
    }
}
