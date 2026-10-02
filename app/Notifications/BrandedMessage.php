<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

/**
 * Builds a MailMessage rendered through the shared on-brand HTML and
 * plain-text email templates, so every account email looks the same.
 */
final class BrandedMessage
{
    /**
     * @param  list<string>  $intro
     * @param  list<string>  $outro
     */
    public static function make(
        string $subject,
        string $heading,
        array $intro,
        ?string $actionText = null,
        ?string $actionUrl = null,
        array $outro = [],
        ?string $eyebrow = null,
        ?string $preheader = null,
    ): MailMessage {
        return (new MailMessage)
            ->subject($subject)
            ->view(
                ['emails.notification', 'emails.notification-text'],
                [
                    'heading' => $heading,
                    'intro' => $intro,
                    'actionText' => $actionText,
                    'actionUrl' => $actionUrl,
                    'outro' => $outro,
                    'eyebrow' => $eyebrow,
                    'preheader' => $preheader ?? ($intro[0] ?? $heading),
                    'appName' => config('app.name'),
                ],
            );
    }
}
