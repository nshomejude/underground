<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** A digest ping: who wrote and where to read it. Never contains the message text. */
final class NewMessageNotification extends Notification
{
    public function __construct(public readonly string $senderName, public readonly string $url) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return BrandedMessage::make(
            subject: 'You have a new message from '.$this->senderName,
            heading: 'You have a new',
            headingEm: 'message.',
            eyebrow: 'Private message',
            preheader: 'You have a new message from '.$this->senderName.'.',
            intro: [
                'You have a new message from '.$this->senderName.' in the member network.',
                'For your privacy we never put the message itself in an email. Sign in to read and reply.',
            ],
            actionText: 'Read the Message',
            actionUrl: $this->url,
            outro: ['You will not receive more than one of these per conversation each hour.'],
        );
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New message from '.$this->senderName,
            'body' => 'You have a new message from '.$this->senderName.'.',
            'url' => $this->url,
        ];
    }
}
