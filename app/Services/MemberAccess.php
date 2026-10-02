<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Application\Membership\Queries\FindMembershipApplicationByEmail;
use Domain\Membership\ValueObjects\MembershipApplicationStatus;

/**
 * Who a member is in the network: tier, rank and what they are allowed to do.
 * Every networking, messaging, voting and verification feature asks this
 * service instead of re-deriving the rules.
 */
final class MemberAccess
{
    /** @var array<int, ?string> */
    private array $tierCache = [];

    public function __construct(private readonly FindMembershipApplicationByEmail $findApplication) {}

    /** The user's membership tier slug (null unless they hold an approved membership). */
    public function tierSlug(User $user): ?string
    {
        if (array_key_exists($user->id, $this->tierCache)) {
            return $this->tierCache[$user->id];
        }

        $application = ($this->findApplication)($user->email);

        $slug = $application !== null && $application->status() === MembershipApplicationStatus::Approved
            ? $application->tier->value
            : null;

        return $this->tierCache[$user->id] = $slug;
    }

    /** 0 = not a member; 1..3 = corporate affiliate .. sovereign partner. */
    public function tierRank(User $user): int
    {
        $slug = $this->tierSlug($user);

        return $slug === null ? 0 : (int) config('network.tier_ranks.'.$slug, 1);
    }

    public function isApprovedMember(User $user): bool
    {
        return $this->tierSlug($user) !== null;
    }

    public function isIdentityVerified(User $user): bool
    {
        return $user->isIdentityVerified();
    }

    /** May open the network area (directory, connections, messages). */
    public function canUseNetwork(User $user): bool
    {
        return $user->hasVerifiedEmail() && $this->isApprovedMember($user);
    }

    /** Appears in the directory: network access, identity verified, and not hidden. */
    public function canBeListed(User $user): bool
    {
        $visibility = $user->profile?->visibility ?? 'members';

        return $this->canUseNetwork($user) && $this->isIdentityVerified($user) && $visibility !== 'hidden';
    }

    /** May send connection / collaboration requests. */
    public function canConnect(User $user): bool
    {
        return $this->canUseNetwork($user) && $this->isIdentityVerified($user);
    }

    /** May open a motion for a vote: staff admins and members of the minimum rank. */
    public function canCreateMotion(User $user): bool
    {
        if ($user->is_admin) {
            return true;
        }

        return $this->isIdentityVerified($user)
            && $this->tierRank($user) >= (int) config('network.motion_creator_min_rank', 3);
    }

    /** May vote on a motion that requires `$minRank` (staff admins always may). */
    public function canVote(User $user, int $minRank = 1): bool
    {
        if ($user->is_admin) {
            return true;
        }

        return $this->isIdentityVerified($user) && $this->tierRank($user) >= $minRank;
    }
}
