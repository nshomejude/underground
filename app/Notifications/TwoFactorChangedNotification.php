<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Security notice for two-factor changes. $event: enabled|disabled|regenerated|recovery_used. */
final class TwoFactorChangedNotification extends Notification
{
    public function __construct(public readonly string $event) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $app = config('app.name');
        [$heading, $line] = match ($this->event) {
            'enabled' => ['Two-factor authentication is on', 'Two-factor authentication was just enabled on your '.$app.' account.'],
            'disabled' => ['Two-factor authentication is off', 'Two-factor authentication was just disabled on your '.$app.' account.'],
            'regenerated' => ['Recovery codes regenerated', 'The recovery codes for your '.$app.' account were just regenerated. Your previous codes no longer work.'],
            default => ['A recovery code was used', 'A recovery code was just used to sign in to your '.$app.' account. It cannot be used again.'],
        };

        return BrandedMessage::make(
            subject: $heading.' - '.$app,
            heading: $heading,
            intro: [$line],
            actionText: 'Review Security Settings',
            actionUrl: route('account.security'),
            outro: ['If this was you, no action is needed. If it was not, reset your password now and write to info@un-der.com so we can secure your account.'],
            eyebrow: 'Security notice',
            preheader: $line,
        );
    }
}
