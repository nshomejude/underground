<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Connection;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Tells a member someone wants to connect or collaborate. */
final class ConnectionRequestedNotification extends Notification
{
    public function __construct(public readonly Connection $connection) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $from = $this->connection->requester;
        $collab = $this->connection->isCollaboration();

        return BrandedMessage::make(
            subject: $collab ? "{$from->name} proposes a collaboration" : "{$from->name} would like to connect",
            heading: $collab ? 'A collaboration request is' : 'A new connection request is',
            headingEm: 'waiting.',
            intro: [
                $collab
                    ? "{$from->name} has proposed a collaboration with you on the Underground member network."
                    : "{$from->name} would like to connect with you on the Underground member network.",
                'Their message: "'.$this->connection->message.'"',
            ],
            actionText: 'Review the Request',
            actionUrl: route('network.connections', ['tab' => 'received']),
            outro: ['You can accept to open a private conversation, or decline politely. Nothing is shared until you accept.'],
            eyebrow: $collab ? 'Collaboration request' : 'Connection request',
            preheader: "{$from->name} wants to ".($collab ? 'collaborate' : 'connect').' with you.',
            summary: array_values(array_filter([
                ['From', $from->name],
                $this->connection->topic ? ['Topic', $this->connection->topic] : null,
            ])),
        );
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        $from = $this->connection->requester;

        return [
            'stage' => 'connection_requested',
            'title' => $this->connection->isCollaboration() ? "{$from->name} proposes a collaboration" : "{$from->name} wants to connect",
            'body' => str($this->connection->message)->limit(120)->toString(),
            'url' => route('network.connections', ['tab' => 'received']),
            'connection_id' => $this->connection->id,
        ];
    }
}
