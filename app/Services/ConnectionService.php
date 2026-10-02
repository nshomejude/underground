<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Connection;
use App\Models\User;
use App\Notifications\ConnectionAcceptedNotification;
use App\Notifications\ConnectionRequestedNotification;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;

/**
 * The connection lifecycle: request, accept/decline, withdraw, remove, block.
 * Every rule lives here so controllers stay thin and the policy is testable.
 */
final class ConnectionService
{
    public const COOLDOWN_DAYS = 30;

    public const MAX_PENDING_OUTGOING = 20;

    public const REQUESTS_PER_HOUR = 10;

    public function __construct(
        private readonly MemberAccess $access,
        private readonly NetworkDirectory $directory,
        private readonly MatchService $matches,
        private readonly ConversationService $conversations,
    ) {}

    /**
     * @param  'connect'|'collaborate'  $kind
     * @param  list<string>  $sectors  sectors in common to highlight on a collaboration request
     *
     * @throws DomainException with a member-friendly message when a rule blocks the request
     */
    public function request(User $from, User $to, string $kind, string $message, ?string $topic = null, array $sectors = []): Connection
    {
        if (! in_array($kind, ['connect', 'collaborate'], true)) {
            throw new DomainException('Unknown request type.');
        }
        if ($from->is($to)) {
            throw new DomainException('You cannot connect with yourself.');
        }
        if (! $this->access->canConnect($from)) {
            throw new DomainException('Verify your identity before sending connection requests.');
        }
        if (! $this->access->canBeListed($to) || $this->directory->isBlockedPair($from->id, $to->id)) {
            throw new DomainException('This member is not available to connect.');
        }

        $existing = Connection::query()->between($from->id, $to->id)->first();
        if ($existing !== null) {
            $this->assertCanReuse($existing, $from);
        }

        if (Connection::query()->where('requester_id', $from->id)->where('status', 'pending')->count() >= self::MAX_PENDING_OUTGOING) {
            throw new DomainException('You have '.self::MAX_PENDING_OUTGOING.' requests waiting for a reply. Withdraw one or wait for answers before sending more.');
        }

        $key = 'connection-request:'.$from->id;
        if (RateLimiter::tooManyAttempts($key, self::REQUESTS_PER_HOUR)) {
            throw new DomainException('You are sending requests too quickly. Please try again later.');
        }

        $mineProfile = $from->profile;
        $theirProfile = $to->profile;
        $score = $mineProfile && $theirProfile ? $this->matches->score($mineProfile, $theirProfile)['score'] : null;

        $attributes = [
            'requester_id' => $from->id,
            'addressee_id' => $to->id,
            'kind' => $kind,
            'status' => 'pending',
            'blocked_by' => null,
            'message' => $message,
            'topic' => $kind === 'collaborate' ? $topic : null,
            'shared_sectors' => $kind === 'collaborate' ? array_values($sectors) : null,
            'match_score' => $score,
            'responded_at' => null,
        ];

        $connection = DB::transaction(function () use ($existing, $attributes): Connection {
            if ($existing !== null) {
                $existing->update($attributes);

                return $existing;
            }

            return Connection::query()->create($attributes);
        });

        RateLimiter::hit($key, 3600);
        $to->notify(new ConnectionRequestedNotification($connection));

        return $connection;
    }

    public function respond(Connection $connection, User $actor, bool $accept): Connection
    {
        if ($connection->addressee_id !== $actor->id || ! $connection->isPending()) {
            throw new DomainException('This request is no longer waiting for your reply.');
        }

        if ($accept && ! $this->access->canConnect($actor)) {
            throw new DomainException('Verify your identity before accepting connections.');
        }

        $connection->update(['status' => $accept ? 'accepted' : 'declined', 'responded_at' => now()]);

        if ($accept) {
            $this->conversations->ensureFor($connection);
            $connection->requester->notify(new ConnectionAcceptedNotification($connection));
        }

        return $connection;
    }

    /** The requester takes back a pending request. */
    public function withdraw(Connection $connection, User $actor): void
    {
        if ($connection->requester_id !== $actor->id || ! $connection->isPending()) {
            throw new DomainException('Only your own pending requests can be withdrawn.');
        }

        $connection->delete();
    }

    /** Either side ends an accepted connection (the conversation goes with it). */
    public function remove(Connection $connection, User $actor): void
    {
        if (! $connection->involves($actor) || ! $connection->isAccepted()) {
            throw new DomainException('That connection cannot be removed.');
        }

        $connection->delete();
    }

    public function block(User $blocker, User $target): Connection
    {
        if ($blocker->is($target)) {
            throw new DomainException('You cannot block yourself.');
        }

        return DB::transaction(function () use ($blocker, $target): Connection {
            $existing = Connection::query()->between($blocker->id, $target->id)->first();
            $attributes = ['status' => 'blocked', 'blocked_by' => $blocker->id, 'responded_at' => now()];

            if ($existing !== null) {
                $existing->update($attributes);

                return $existing;
            }

            return Connection::query()->create($attributes + [
                'requester_id' => $blocker->id,
                'addressee_id' => $target->id,
                'kind' => 'connect',
            ]);
        });
    }

    public function unblock(User $blocker, User $target): void
    {
        Connection::query()->between($blocker->id, $target->id)
            ->where('status', 'blocked')->where('blocked_by', $blocker->id)->delete();
    }

    /**
     * The relationship from the viewer's side, for the profile page.
     *
     * @return array{state: 'none'|'sent'|'received'|'connected'|'blocked'|'blocked_by_them'|'cooldown', connection: ?Connection}
     */
    public function stateBetween(User $viewer, User $other): array
    {
        $c = Connection::query()->between($viewer->id, $other->id)->first();

        if ($c === null) {
            return ['state' => 'none', 'connection' => null];
        }

        $state = match ($c->status) {
            'accepted' => 'connected',
            'pending' => $c->requester_id === $viewer->id ? 'sent' : 'received',
            'blocked' => $c->blocked_by === $viewer->id ? 'blocked' : 'blocked_by_them',
            'declined' => $this->cooldownActive($c, $viewer) ? 'cooldown' : 'none',
            default => 'none',
        };

        return ['state' => $state, 'connection' => $c];
    }

    private function assertCanReuse(Connection $existing, User $from): void
    {
        match ($existing->status) {
            'accepted' => throw new DomainException('You are already connected.'),
            'pending' => throw new DomainException($existing->requester_id === $from->id
                ? 'You already have a request waiting with this member.'
                : 'This member has already sent you a request. Accept it from your connections.'),
            'blocked' => throw new DomainException('This member is not available to connect.'),
            'declined' => $this->cooldownActive($existing, $from)
                ? throw new DomainException('This member declined your last request. You can try again on '.$existing->responded_at->copy()->addDays(self::COOLDOWN_DAYS)->format('j F Y').'.')
                : null,
            default => null,
        };
    }

    private function cooldownActive(Connection $declined, User $requester): bool
    {
        return $declined->requester_id === $requester->id
            && $declined->responded_at !== null
            && $declined->responded_at->gt(now()->subDays(self::COOLDOWN_DAYS));
    }
}
