<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Connection;
use App\Models\MemberProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Query building for the member directory. "Listed" mirrors
 * MemberAccess::canBeListed() but runs in SQL so it scales: verified email,
 * an approved membership, an approved unexpired identity verification and a
 * profile that is not hidden.
 */
final class NetworkDirectory
{
    /** @return Builder<MemberProfile> */
    public function listed(): Builder
    {
        return MemberProfile::query()
            ->whereNotNull('member_profiles.slug')
            ->where('member_profiles.visibility', '!=', 'hidden')
            ->whereHas('user', function (Builder $user): void {
                $user->whereNotNull('users.email_verified_at')
                    ->whereExists(fn ($q) => $this->approvedApplication($q))
                    ->whereExists(function ($q): void {
                        $q->select(DB::raw(1))->from('identity_verifications as iv')
                            ->whereColumn('iv.user_id', 'users.id')
                            ->where('iv.status', 'approved')
                            ->where(fn ($w) => $w->whereNull('iv.expires_at')->orWhere('iv.expires_at', '>', now()));
                    });
            });
    }

    /**
     * Listed members as seen by the viewer: not themselves, no blocked pairs.
     *
     * @return Builder<MemberProfile>
     */
    public function listedFor(User $viewer): Builder
    {
        return $this->listed()
            ->where('member_profiles.user_id', '!=', $viewer->id)
            ->whereNotExists(function ($q) use ($viewer): void {
                $q->select(DB::raw(1))->from('connections as bc')
                    ->where('bc.status', 'blocked')
                    ->where(function ($w) use ($viewer): void {
                        $w->where(fn ($x) => $x->where('bc.requester_id', $viewer->id)->whereColumn('bc.addressee_id', 'member_profiles.user_id'))
                            ->orWhere(fn ($x) => $x->where('bc.addressee_id', $viewer->id)->whereColumn('bc.requester_id', 'member_profiles.user_id'));
                    });
            });
    }

    public function isBlockedPair(int $a, int $b): bool
    {
        return Connection::query()->between($a, $b)->where('status', 'blocked')->exists();
    }

    /**
     * @param  Builder<MemberProfile>  $query
     * @param  array<string, mixed>  $f
     * @return Builder<MemberProfile>
     */
    public function applyFilters(Builder $query, array $f): Builder
    {
        if (($f['q'] ?? '') !== '') {
            $like = '%'.addcslashes((string) $f['q'], '%_\\').'%';
            $query->where(function (Builder $w) use ($like): void {
                $w->where('member_profiles.display_name', 'like', $like)
                    ->orWhere('member_profiles.headline', 'like', $like)
                    ->orWhere('member_profiles.organisation_name', 'like', $like)
                    ->orWhereHas('user', fn (Builder $u) => $u->where('users.name', 'like', $like));
            });
        }

        if (! empty($f['sector'])) {
            $query->where(function (Builder $w) use ($f): void {
                foreach ((array) $f['sector'] as $slug) {
                    $w->orWhereJsonContains('member_profiles.sectors', $slug);
                }
            });
        }

        if (($f['role'] ?? '') !== '') {
            $query->whereJsonContains('member_profiles.supply_chain_roles', $f['role']);
        }

        if (($f['country'] ?? '') !== '') {
            $query->where('member_profiles.country', $f['country']);
        }

        if (! empty($f['open'])) {
            $query->where('member_profiles.open_to_collaboration', true);
        }

        if (($f['tier'] ?? '') !== '') {
            $query->whereHas('user', fn (Builder $u) => $u->whereExists(fn ($q) => $this->approvedApplication($q, (string) $f['tier'])));
        }

        if (! empty($f['company'])) {
            $query->whereHas('user', fn (Builder $u) => $u->whereExists(function ($q): void {
                $q->select(DB::raw(1))->from('company_verifications as cv')
                    ->whereColumn('cv.user_id', 'users.id')
                    ->where('cv.status', 'approved')
                    ->where(fn ($w) => $w->whereNull('cv.expires_at')->orWhere('cv.expires_at', '>', now()));
            }));
        }

        return $query;
    }

    /** Correlated "has an approved membership (optionally of this tier)" subquery on users. */
    private function approvedApplication($q, ?string $tierSlug = null): void
    {
        $q->select(DB::raw(1))->from('membership_applications as ma')
            ->whereRaw('LOWER(ma.email) = LOWER(users.email)')
            ->where('ma.status', 'approved');

        if ($tierSlug !== null) {
            $q->whereExists(fn ($t) => $t->select(DB::raw(1))->from('membership_tiers as mt')
                ->whereColumn('mt.id', 'ma.tier_id')->where('mt.slug', $tierSlug));
        }
    }

    /**
     * Tier slug per user id (latest approved application wins), one query.
     *
     * @param  iterable<int, User>  $users
     * @return array<int, string>
     */
    public function tierSlugs(iterable $users): array
    {
        $emails = [];
        foreach ($users as $u) {
            $emails[$u->id] = strtolower($u->email);
        }
        if ($emails === []) {
            return [];
        }

        $rows = DB::table('membership_applications as ma')
            ->join('membership_tiers as mt', 'mt.id', '=', 'ma.tier_id')
            ->where('ma.status', 'approved')
            ->whereIn(DB::raw('LOWER(ma.email)'), array_values($emails))
            ->orderBy('ma.submitted_at')
            ->get(['ma.email', 'mt.slug']);

        $byEmail = [];
        foreach ($rows as $r) {
            $byEmail[strtolower($r->email)] = $r->slug;
        }

        $out = [];
        foreach ($emails as $id => $email) {
            if (isset($byEmail[$email])) {
                $out[$id] = $byEmail[$email];
            }
        }

        return $out;
    }

    /** @return array<string, string> slug => display name */
    public function tierNames(): array
    {
        return DB::table('membership_tiers')->pluck('name', 'slug')->all();
    }

    /**
     * @param  list<int>  $userIds
     * @return array<int, true>
     */
    public function companyVerified(array $userIds): array
    {
        if ($userIds === []) {
            return [];
        }

        return DB::table('company_verifications')
            ->whereIn('user_id', $userIds)->where('status', 'approved')
            ->where(fn ($w) => $w->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->pluck('user_id')->mapWithKeys(fn ($id) => [(int) $id => true])->all();
    }

    /** @return list<string> distinct countries of listed members, for the filter. */
    public function countries(): array
    {
        return $this->listed()->whereNotNull('member_profiles.country')->where('member_profiles.country', '!=', '')
            ->distinct()->orderBy('member_profiles.country')->pluck('member_profiles.country')->all();
    }
}
