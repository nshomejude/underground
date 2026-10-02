<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\MemberProfile;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Matchmaking between member profiles: pure PHP over the profile data, no
 * external services. A score is 0-100 with human readable reasons.
 */
final class MatchService
{
    public const CANDIDATE_CAP = 200;

    /** Which supply-chain roles satisfy each thing a member can say they are seeking. */
    private const SEEKING_ROLES = [
        'supplier' => ['producer', 'manufacturer', 'distributor'],
        'buyer' => ['buyer', 'distributor'],
        'investor' => ['financier'],
        'advisor' => ['advisor', 'regulator'],
        'introductions' => ['regulator', 'buyer', 'financier', 'operator'],
        'partner' => [],
    ];

    public function __construct(
        private readonly NetworkDirectory $directory,
        private readonly MemberAccess $access,
    ) {}

    /**
     * The best matches for a member, highest first.
     *
     * @return Collection<int, array{profile: MemberProfile, score: int, reasons: list<string>, tier: ?string}>
     */
    public function suggestions(User $viewer, int $limit = 6): Collection
    {
        $mine = $viewer->profile;
        if ($mine === null) {
            return collect();
        }

        $query = $this->directory->listedFor($viewer)->with('user');

        // Pre-filter: overlapping sectors or complementary roles, falling back to everyone
        // when the viewer has not described themselves yet.
        $sectors = (array) ($mine->sectors ?? []);
        $roles = $this->complementaryTo((array) ($mine->supply_chain_roles ?? []));
        if ($sectors !== [] || $roles !== []) {
            $query->where(function ($w) use ($sectors, $roles): void {
                foreach ($sectors as $s) {
                    $w->orWhereJsonContains('member_profiles.sectors', $s);
                }
                foreach ($roles as $r) {
                    $w->orWhereJsonContains('member_profiles.supply_chain_roles', $r);
                }
            });
        }

        $candidates = $query->latest('member_profiles.updated_at')->limit(self::CANDIDATE_CAP)->get();

        return $this->rank($viewer, $mine, $candidates)->take($limit)->values();
    }

    /**
     * Score and rank a set of candidate profiles against the viewer.
     *
     * @param  iterable<int, MemberProfile>  $candidates
     * @return Collection<int, array{profile: MemberProfile, score: int, reasons: list<string>, tier: ?string}>
     */
    public function rank(User $viewer, MemberProfile $mine, iterable $candidates, int $minScore = 1): Collection
    {
        $candidates = collect($candidates)->values();
        $tiers = $this->directory->tierSlugs(
            $candidates->map(fn (MemberProfile $p) => $p->user)->push($viewer)->filter()->unique('id'),
        );
        $mineRank = $this->rankOf($tiers[$viewer->id] ?? null);

        return $candidates
            ->map(function (MemberProfile $p) use ($mine, $tiers, $mineRank): array {
                $slug = $tiers[$p->user_id] ?? null;
                $m = $this->score($mine, $p, $mineRank, $this->rankOf($slug));

                return ['profile' => $p, 'score' => $m['score'], 'reasons' => $m['reasons'], 'tier' => $slug];
            })
            ->filter(fn (array $m) => $m['score'] >= $minScore)
            ->sortByDesc(fn (array $m) => [$m['score'], $m['profile']->updated_at?->timestamp ?? 0])
            ->values();
    }

    /**
     * @return array{score: int, reasons: list<string>}
     */
    public function score(MemberProfile $a, MemberProfile $b, ?int $rankA = null, ?int $rankB = null): array
    {
        $score = 0;
        $reasons = [];
        $sectorNames = (array) config('network.sectors');
        $roleNames = (array) config('network.supply_chain_roles');
        $kindNames = (array) config('network.seeking_kinds');

        // 1. Shared sectors: the heaviest signal.
        $shared = array_values(array_intersect((array) ($a->sectors ?? []), (array) ($b->sectors ?? [])));
        if ($shared !== []) {
            $score += match (count($shared)) {
                1 => 24,
                2 => 34,
                default => 40,
            };
            $labels = array_map(fn (string $s) => $sectorNames[$s] ?? $s, $shared);
            $reasons[] = count($labels) === 1
                ? 'You both work in '.$labels[0]
                : 'You share '.count($labels).' sectors: '.$this->join($labels);
        }

        // 2. Complementary supply-chain roles.
        $aRoles = (array) ($a->supply_chain_roles ?? []);
        $bRoles = (array) ($b->supply_chain_roles ?? []);
        $complement = (array) config('network.complementary_roles');
        foreach ($aRoles as $ra) {
            foreach ($bRoles as $rb) {
                if ($ra !== $rb && in_array($rb, $complement[$ra] ?? [], true)) {
                    $score += 20;
                    $reasons[] = sprintf('Complementary roles: your %s fits their %s', $this->short($roleNames[$ra] ?? $ra), $this->short($roleNames[$rb] ?? $rb));
                    break 2;
                }
            }
        }

        // 3. What I seek versus what they are; and the reverse (a mutual signal).
        $mineWants = $this->satisfies((array) ($a->seeking_kinds ?? []), $bRoles, (bool) $b->open_to_collaboration);
        $theirsWant = $this->satisfies((array) ($b->seeking_kinds ?? []), $aRoles, (bool) $a->open_to_collaboration);
        if ($mineWants !== []) {
            $score += 15;
            $reasons[] = 'They supply what you are seeking: '.$this->join(array_map(fn ($k) => $kindNames[$k] ?? $k, $mineWants));
        }
        if ($theirsWant !== []) {
            $score += 8;
            $reasons[] = 'You offer what they are seeking: '.$this->join(array_map(fn ($k) => $kindNames[$k] ?? $k, $theirsWant));
        }
        if ($mineWants !== [] && $theirsWant !== []) {
            $score += 4;
            $reasons[] = 'The fit is mutual';
        }

        // 4. Same place.
        if ($this->same($a->country, $b->country)) {
            $score += 8;
            $reasons[] = 'You are both based in '.$b->country;
            if ($this->same($a->city, $b->city)) {
                $score += 3;
            }
        }

        // 5. Both open to collaboration.
        if ($a->open_to_collaboration && $b->open_to_collaboration) {
            $score += 7;
            $reasons[] = 'You are both open to collaboration';
        }

        // 6. Tier adjacency: a small bonus for neighbouring tiers, never a penalty.
        $rankA ??= $this->access->tierRank($a->user);
        $rankB ??= $this->access->tierRank($b->user);
        if ($rankA > 0 && $rankB > 0 && abs($rankA - $rankB) <= 1) {
            $score += 5;
            if ($rankA === $rankB) {
                $reasons[] = 'You hold the same membership tier';
            }
        }

        return ['score' => min(100, $score), 'reasons' => $reasons];
    }

    /** Sectors both profiles list. @return list<string> */
    public function sharedSectors(MemberProfile $a, MemberProfile $b): array
    {
        return array_values(array_intersect((array) ($a->sectors ?? []), (array) ($b->sectors ?? [])));
    }

    /**
     * Seeking kinds of `$seeker` that `$roles` satisfy.
     *
     * @param  list<string>  $kinds
     * @param  list<string>  $roles
     * @return list<string>
     */
    private function satisfies(array $kinds, array $roles, bool $open): array
    {
        $hits = [];
        foreach ($kinds as $kind) {
            $wanted = self::SEEKING_ROLES[$kind] ?? [];
            if ($wanted === [] ? ($kind === 'partner' && $open) : array_intersect($wanted, $roles) !== []) {
                $hits[] = $kind;
            }
        }

        return $hits;
    }

    /**
     * @param  list<string>  $roles
     * @return list<string>
     */
    private function complementaryTo(array $roles): array
    {
        $map = (array) config('network.complementary_roles');
        $out = [];
        foreach ($roles as $r) {
            $out = array_merge($out, $map[$r] ?? []);
        }

        return array_values(array_unique($out));
    }

    private function rankOf(?string $slug): int
    {
        return $slug === null ? 0 : (int) config('network.tier_ranks.'.$slug, 1);
    }

    private function same(?string $x, ?string $y): bool
    {
        return $x !== null && $y !== null && $x !== '' && mb_strtolower(trim($x)) === mb_strtolower(trim($y));
    }

    /** "Producer / Supplier" -> "Producer", for compact sentences. */
    private function short(string $label): string
    {
        return trim(explode('/', $label)[0]);
    }

    /** @param  list<string>  $items */
    private function join(array $items): string
    {
        if (count($items) <= 2) {
            return implode(' and ', $items);
        }

        return implode(', ', array_slice($items, 0, -1)).' and '.end($items);
    }
}
