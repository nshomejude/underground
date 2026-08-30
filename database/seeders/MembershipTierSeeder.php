<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Infrastructure\Persistence\Eloquent\Models\MembershipTierRecord;

/**
 * Seeds the three vetted membership tiers Underground extends to
 * governments, principals, and corporate institutions.
 */
final class MembershipTierSeeder extends Seeder
{
    public function run(): void
    {
        $tiers = [
            [
                'slug' => 'sovereign-partner',
                'name' => 'Sovereign Partner',
                'audience' => 'Heads of state, sovereign wealth funds, and national governments.',
                'icon' => 'landmark',
                'position' => 1,
            ],
            [
                'slug' => 'principal-circle',
                'name' => 'Principal Circle',
                'audience' => 'Principals, family offices, and individual investors of consequence.',
                'icon' => 'gem',
                'position' => 2,
            ],
            [
                'slug' => 'corporate-affiliate',
                'name' => 'Corporate Affiliate',
                'audience' => 'Corporations and institutions operating across contested markets.',
                'icon' => 'building-2',
                'position' => 3,
            ],
        ];

        foreach ($tiers as $tier) {
            MembershipTierRecord::query()->updateOrCreate(
                ['slug' => $tier['slug']],
                [
                    'name' => $tier['name'],
                    'audience' => $tier['audience'],
                    'icon' => $tier['icon'],
                    'position' => $tier['position'],
                ],
            );
        }
    }
}
