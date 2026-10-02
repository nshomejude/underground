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
 * Rendered through the shared on-brand email layout, with a plain-text
 * alternative for clients that block or strip HTML.
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
            view: 'emails.notification',
            text: 'emails.notification-text',
            with: [
                'heading' => $firstName.', you are',
                'headingEm' => 'in.',
                'eyebrow' => 'Welcome',
                'preheader' => 'Your account is ready. Power, calm and connection: begin below.',
                'intro' => [
                    'Thank you for creating your '.config('app.name').' account. We are a global network built on quiet influence and trusted connections, and we are glad to have you with us.',
                ],
                'actionText' => 'Open Your Account',
                'actionUrl' => route('account.show'),
                'steps' => [
                    ['Verify your email', 'A separate message carries your confirmation link.'],
                    ['Explore membership', 'Our tiers are extended to a vetted few. Apply when you are ready.'],
                    ['Write to us in confidence', 'Every inquiry is handled with discretion by a partner.'],
                ],
                'outro' => [],
                'summary' => [],
                'signedBy' => 'Tony Smith',
                'signedTitle' => 'Founder & Managing Partner',
                'appName' => config('app.name'),
                'footerNote' => 'You received this because an account was created on our website with this email address. If it was not you, you can ignore this message.',
            ],
        );
    }
}
