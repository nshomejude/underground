<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Welcome message sent from welcome@un-der.com when a new account registers.
 * HTML and plain-text bodies are both provided so clients that block or
 * strip HTML still render something readable.
 */
final class WelcomeMail extends Mailable
{
    public function __construct(public readonly User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address((string) config('mail.welcome_address', 'welcome@un-der.com'), config('app.name')),
            replyTo: [new Address((string) config('mail.welcome_address', 'welcome@un-der.com'), config('app.name'))],
            subject: 'Welcome to '.config('app.name'),
        );
    }

    public function content(): Content
    {
        $firstName = trim(explode(' ', trim($this->user->name))[0]) ?: 'there';

        return new Content(
            view: 'emails.welcome',
            text: 'emails.welcome-text',
            with: [
                'firstName' => $firstName,
                'appName' => config('app.name'),
                'siteUrl' => url('/'),
                'accountUrl' => route('account.show'),
                'membershipUrl' => route('membership.index'),
                'contactUrl' => route('contact'),
            ],
        );
    }
}
