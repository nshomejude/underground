<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Message;
use App\Notifications\NewMessageNotification;
use App\Services\ConversationService;
use Illuminate\Console\Command;

/**
 * Emails a recipient when messages have sat unread for 10 minutes: at most one
 * email per recipient per conversation per hour, never with the message text.
 */
final class NotifyUnreadMessages extends Command
{
    protected $signature = 'messages:notify-unread';

    protected $description = 'Email members about messages left unread for 10 minutes';

    public function handle(): int
    {
        $sent = 0;

        Message::query()
            ->whereNull('read_at')
            ->whereNull('notified_at')
            ->where('created_at', '<=', now()->subMinutes(10))
            ->with(['sender.profile', 'conversation.link.requester', 'conversation.link.addressee'])
            ->orderBy('id')
            ->get()
            ->groupBy(fn (Message $m) => $m->conversation_id.':'.$m->sender_id)
            ->each(function ($group) use (&$sent): void {
                /** @var Message $first */
                $first = $group->first();
                $connection = $first->conversation->link;

                if (! $connection->isAccepted()) {
                    return;
                }

                $recipient = $connection->otherParty($first->sender);

                if (! $recipient->hasVerifiedEmail()) {
                    return;
                }

                $recentlyNotified = Message::query()
                    ->where('conversation_id', $first->conversation_id)
                    ->where('sender_id', $first->sender_id)
                    ->where('notified_at', '>', now()->subHour())
                    ->exists();

                if ($recentlyNotified) {
                    return;
                }

                $recipient->notify(new NewMessageNotification(
                    ConversationService::nameOf($first->sender),
                    route('messages.show', $first->conversation_id),
                ));

                Message::query()->whereIn('id', $group->pluck('id'))->update(['notified_at' => now()]);
                $sent++;
            });

        $this->info("Sent {$sent} unread-message digest(s).");

        return self::SUCCESS;
    }
}
