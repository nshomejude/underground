<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

/**
 * Builds a MailMessage rendered through the shared on-brand HTML and
 * plain-text email templates (resources/views/emails), so every account
 * email looks and behaves the same.
 */
final class BrandedMessage
{
    /**
     * @param  list<string>  $intro
     * @param  list<string>  $outro
     * @param  list<array{0: string, 1: string, 2?: bool}>  $summary  [label, value, monospace?]
     * @param  list<array{0: string, 1: string}>  $steps  [title, text]
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
        ?string $headingEm = null,
        array $summary = [],
        array $steps = [],
        ?string $signedBy = null,
        ?string $signedTitle = null,
    ): MailMessage {
        return (new MailMessage)
            ->subject($subject)
            ->view(
                ['emails.notification', 'emails.notification-text'],
                [
                    'heading' => $heading,
                    'headingEm' => $headingEm,
                    'intro' => $intro,
                    'actionText' => $actionText,
                    'actionUrl' => $actionUrl,
                    'outro' => $outro,
                    'eyebrow' => $eyebrow,
                    'preheader' => $preheader ?? ($intro[0] ?? $heading),
                    'summary' => $summary,
                    'steps' => $steps,
                    'signedBy' => $signedBy,
                    'signedTitle' => $signedTitle,
                    'appName' => config('app.name'),
                ],
            );
    }
}
