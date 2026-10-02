<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * On-brand email verification message. Extends the framework's default
 * VerifyEmail notification — the signed verification URL generation,
 * expiry, and MustVerifyEmail wiring are all inherited unchanged; only
 * the mail template is customised here.
 */
final class VerifyEmailNotification extends VerifyEmail
{
    /**
     * @return MailMessage
     */
    protected function buildMailMessage($url)
    {
        $minutes = (int) config('auth.verification.expire', 60);

        return BrandedMessage::make(
            subject: 'Verify your '.config('app.name').' email address',
            heading: 'Confirm your email address',
            intro: ['Please confirm this is your email address to finish setting up your account.'],
            actionText: 'Verify Email Address',
            actionUrl: $url,
            outro: [
                "This link expires in {$minutes} minutes. If it has expired, sign in and request a new one from your account.",
                'If you did not create an account, no further action is required.',
            ],
            eyebrow: 'One last step',
            preheader: 'Confirm your email to finish setting up your account.',
        );
    }
}
