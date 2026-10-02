<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Security notice sent whenever an account's password is changed or reset,
 * so the owner can react if it was not them.
 */
final class PasswordChangedNotification extends Notification
{
    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return BrandedMessage::make(
            subject: 'Your '.config('app.name').' password was changed',
            heading: 'Your password was changed',
            intro: ['The password for your '.config('app.name').' account was just changed.'],
            actionText: 'Reset Password',
            actionUrl: route('password.request'),
            outro: ['If this was you, no action is needed. If it was not, reset your password now and write to info@un-der.com so we can secure your account.'],
            eyebrow: 'Security notice',
            preheader: 'Your account password was changed.',
        );
    }
}
