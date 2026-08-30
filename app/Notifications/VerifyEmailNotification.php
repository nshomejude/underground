<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * On-brand email verification message. Extends the framework's default
 * VerifyEmail notification — the signed verification URL generation,
 * expiry, and MustVerifyEmail wiring are all inherited unchanged; only
 * the mail copy is customised here.
 */
final class VerifyEmailNotification extends VerifyEmail
{
    /**
     * @return MailMessage
     */
    protected function buildMailMessage($url)
    {
        return (new MailMessage)
            ->subject('Verify your '.config('app.name').' email address')
            ->greeting('Welcome to '.config('app.name'))
            ->line('Please confirm this is your email address to finish setting up your account.')
            ->action('Verify Email Address', $url)
            ->line('If you did not create an account, no further action is required.');
    }
}
