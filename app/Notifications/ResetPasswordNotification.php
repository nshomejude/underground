<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * On-brand password reset email. Extends the framework's default
 * ResetPassword notification (built on the core Password broker /
 * CanResetPassword trait) purely to customise the mail copy — the token,
 * URL generation, and broker wiring are all inherited unchanged.
 */
final class ResetPasswordNotification extends ResetPassword
{
    /**
     * @return MailMessage
     */
    protected function buildMailMessage($url)
    {
        $expiresInMinutes = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

        return (new MailMessage)
            ->subject('Reset your '.config('app.name').' password')
            ->greeting('Password reset request')
            ->line('We received a request to reset the password for your member account.')
            ->action('Reset Password', $url)
            ->line("This link will expire in {$expiresInMinutes} minutes.")
            ->line('If you did not request a password reset, no further action is required — your account is unchanged.');
    }
}
