<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Connection;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Creates (once) the conversation that belongs to an accepted connection and
 * answers who may see which conversation. Networking calls ensureFor() when a
 * request is accepted; messaging owns everything inside the conversation.
 */
final class ConversationService
{
    public function ensureFor(Connection $connection): Conversation
    {
        return Conversation::query()->firstOrCreate(['connection_id' => $connection->id]);
    }

    /** Ids of the user's conversations whose connection is still accepted. */
    public static function openConversationIds(User $user): QueryBuilder
    {
        return DB::table('conversations')
            ->join('connections', 'connections.id', '=', 'conversations.connection_id')
            ->where('connections.status', 'accepted')
            ->where(fn ($q) => $q->where('connections.requester_id', $user->id)->orWhere('connections.addressee_id', $user->id))
            ->select('conversations.id');
    }

    /**
     * The user's open conversations, newest activity first, with the other member,
     * the last message and the unread count attached.
     *
     * @return Collection<int, Conversation>
     */
    public function listFor(User $user): Collection
    {
        $conversations = Conversation::query()
            ->whereIn('id', self::openConversationIds($user))
            ->with(['link.requester.profile', 'link.addressee.profile'])
            ->orderByRaw('last_message_at IS NULL')
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->get();

        if ($conversations->isEmpty()) {
            return $conversations;
        }

        $ids = $conversations->pluck('id');
        $lastIds = Message::query()->whereIn('conversation_id', $ids)->selectRaw('MAX(id) as id')->groupBy('conversation_id')->pluck('id');
        $last = Message::query()->whereIn('id', $lastIds)->get()->keyBy('conversation_id');
        $unread = Message::query()->whereIn('conversation_id', $ids)->whereNull('read_at')
            ->where('sender_id', '!=', $user->id)->selectRaw('conversation_id, COUNT(*) as n')->groupBy('conversation_id')
            ->pluck('n', 'conversation_id');

        return $conversations->each(function (Conversation $c) use ($user, $last, $unread): void {
            $c->setRelation('lastMessage', $last->get($c->id));
            $c->setAttribute('unread', (int) ($unread[$c->id] ?? 0));
            $c->setAttribute('other', $c->link->otherParty($user));
        });
    }

    /** 404 for outsiders, 403 once the connection is no longer accepted. */
    public function authorize(Conversation $conversation, User $user): void
    {
        $conversation->loadMissing('link');
        abort_unless($conversation->hasParty($user), 404);
        abort_unless($conversation->isOpen(), 403, 'This conversation is closed.');
    }

    /** Display name: the profile's display name when set, else the account name. */
    public static function nameOf(User $user): string
    {
        return $user->profile?->display_name ?: $user->name;
    }

    public static function initials(string $name): string
    {
        return collect(preg_split('/\s+/', trim($name)))->filter()->take(2)
            ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('') ?: 'U';
    }
}
