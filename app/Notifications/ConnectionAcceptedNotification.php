<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Connection;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Route;

/** Tells the requester their request was accepted. */
final class ConnectionAcceptedNotification extends Notification
{
    public function __construct(public readonly Connection $connection) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $by = $this->connection->addressee;

        return BrandedMessage::make(
            subject: "{$by->name} accepted your request",
            heading: "You are now connected with {$by->name}.",
            intro: [
                "{$by->name} accepted your ".($this->connection->isCollaboration() ? 'collaboration' : 'connection').' request on the Underground member network.',
                'You can now message each other privately.',
            ],
            actionText: 'Open the Conversation',
            actionUrl: $this->url(),
            eyebrow: 'Request accepted',
            preheader: "{$by->name} accepted your request.",
        );
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        $by = $this->connection->addressee;

        return [
            'stage' => 'connection_accepted',
            'title' => "{$by->name} accepted your request",
            'body' => 'You can now message each other.',
            'url' => $this->url(),
            'connection_id' => $this->connection->id,
        ];
    }

    private function url(): string
    {
        $conversation = $this->connection->conversation;

        if ($conversation !== null && Route::has('messages.show')) {
            return route('messages.show', $conversation);
        }

        return route('network.connections');
    }
}
