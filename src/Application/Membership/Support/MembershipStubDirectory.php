<?php

declare(strict_types=1);

// STUB: in-memory backing store for the Membership stub classes only.
// Delete this file alongside the other Membership stubs at merge time —
// the real module persists tiers and applications through Eloquent
// repositories instead.

namespace Application\Membership\Support;

use Domain\Membership\Entities\MembershipApplication;

final class MembershipStubDirectory
{
    /** @var list<array{slug: string, name: string, audience: string, icon: string, position: int}> */
    private static array $tiers = [
        [
            'slug' => 'sovereign-partner',
            'name' => 'Sovereign Partner',
            'audience' => 'Heads of state, ministries, and sovereign institutions.',
            'icon' => 'landmark',
            'position' => 1,
        ],
        [
            'slug' => 'principal-circle',
            'name' => 'Principal Circle',
            'audience' => 'Principals, family offices, and private investors.',
            'icon' => 'gem',
            'position' => 2,
        ],
        [
            'slug' => 'corporate-affiliate',
            'name' => 'Corporate Affiliate',
            'audience' => 'Corporations and institutional partners.',
            'icon' => 'building-2',
            'position' => 3,
        ],
    ];

    /** @var array<string, MembershipApplication> */
    private static array $applications = [];

    /** @return list<array{slug: string, name: string, audience: string, icon: string, position: int}> */
    public static function tiers(): array
    {
        return self::$tiers;
    }

    public static function remember(MembershipApplication $application): void
    {
        self::$applications[$application->reference] = $application;
    }

    public static function find(string $reference): ?MembershipApplication
    {
        return self::$applications[$reference] ?? null;
    }
}
