<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

/** Everything that happens inside a conversation: sending, reading, formatting, reporting. */
final class MessageService
{
    public const MAX_LENGTH = 2000;

    /** Unread messages addressed to the user across their open conversations (indexed, one query). */
    public static function unreadCount(?User $user = null): int
    {
        $user ??= auth()->user();

        if ($user === null) {
            return 0;
        }

        return Message::query()
            ->whereNull('read_at')
            ->where('sender_id', '!=', $user->id)
            ->whereIn('conversation_id', ConversationService::openConversationIds($user))
            ->count();
    }

    /** @throws ValidationException */
    public function send(Conversation $conversation, User $sender, string $body): Message
    {
        $body = trim($body);

        if (! $conversation->isOpen() || ! $conversation->hasParty($sender)) {
            throw ValidationException::withMessages(['body' => 'This conversation is closed, so messages can no longer be sent.']);
        }

        $repeats = Message::query()
            ->where('conversation_id', $conversation->id)
            ->where('sender_id', $sender->id)
            ->where('body', $body)
            ->where('created_at', '>=', now()->subMinutes(2))
            ->count();

        if ($repeats >= 2) {
            throw ValidationException::withMessages(['body' => 'You have sent this exact message several times already. Please wait a moment before repeating it.']);
        }

        $message = $conversation->messages()->create(['sender_id' => $sender->id, 'body' => $body]);
        $conversation->forceFill(['last_message_at' => $message->created_at])->save();

        return $message;
    }

    /** Marks everything the other party sent as read; returns how many changed. */
    public function markRead(Conversation $conversation, User $viewer): int
    {
        return Message::query()
            ->where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $viewer->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /** @return Collection<int, Message> */
    public function newerThan(Conversation $conversation, int $afterId): Collection
    {
        return $conversation->messages()->where('id', '>', $afterId)->orderBy('id')->limit(100)->get();
    }

    /** @return Collection<int, Message> oldest first */
    public function recent(Conversation $conversation, int $limit = 200): Collection
    {
        return $conversation->messages()->orderByDesc('id')->limit($limit)->get()->reverse()->values();
    }

    public function lastSeenId(Conversation $conversation, User $viewer): int
    {
        return (int) Message::query()
            ->where('conversation_id', $conversation->id)
            ->where('sender_id', $viewer->id)
            ->whereNotNull('read_at')
            ->max('id');
    }

    public function report(Message $message, User $reporter, ?string $reason): void
    {
        $message->forceFill([
            'reported_at' => now(),
            'reported_by' => $reporter->id,
            'report_reason' => $reason !== null && trim($reason) !== '' ? mb_substr(trim($reason), 0, 500) : null,
        ])->save();
    }

    /** Plain text in, safe HTML out: escaped, http(s) links made clickable, line breaks kept. */
    public static function format(string $body): string
    {
        $parts = preg_split('~(https?://[^\s<>"\']+)~iu', $body, -1, PREG_SPLIT_DELIM_CAPTURE) ?: [$body];
        $html = '';

        foreach ($parts as $i => $part) {
            if ($i % 2 === 0) {
                $html .= e($part);

                continue;
            }

            $trail = '';
            while ($part !== '' && str_contains('.,;:!?)]}', substr($part, -1))) {
                $trail = substr($part, -1).$trail;
                $part = substr($part, 0, -1);
            }

            $html .= '<a href="'.e($part).'" target="_blank" rel="noopener noreferrer nofollow ugc">'.e($part).'</a>'.e($trail);
        }

        return nl2br($html, false);
    }

    /** @return array<string, mixed> */
    public static function payload(Message $message, User $viewer): array
    {
        return [
            'id' => $message->id,
            'mine' => $message->sender_id === $viewer->id,
            'html' => self::format($message->body),
            'at' => $message->created_at->toIso8601String(),
            'time' => $message->created_at->format('H:i'),
            'day' => $message->created_at->format('j F Y'),
        ];
    }
}
