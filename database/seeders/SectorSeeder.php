<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Infrastructure\Persistence\Eloquent\Models\SectorRecord;

/**
 * Seeds the six verticals Underground operates in, from the content brief.
 */
final class SectorSeeder extends Seeder
{
    public function run(): void
    {
        $sectors = [
            [
                'slug' => 'government-public-sector',
                'name' => 'Government & Public Sector',
                'summary' => 'Advising heads of state, ministries, and public institutions on policy and strategic positioning.',
                'motif' => 'landmark',
                'position' => 1,
            ],
            [
                'slug' => 'energy-natural-resources',
                'name' => 'Energy & Natural Resources',
                'summary' => 'Structuring strategy and relationships across the energy transition and resource-rich markets.',
                'motif' => 'flame',
                'position' => 2,
            ],
            [
                'slug' => 'infrastructure-transportation',
                'name' => 'Infrastructure & Transportation',
                'summary' => 'Advisory across the ports, corridors, and networks that connect capital to strategic outcomes.',
                'motif' => 'ship-wheel',
                'position' => 3,
            ],
            [
                'slug' => 'defense-security',
                'name' => 'Defense & Security',
                'summary' => 'Discreet counsel for governments and institutions navigating defense and security relationships.',
                'motif' => 'shield-check',
                'position' => 4,
            ],
            [
                'slug' => 'technology-innovation',
                'name' => 'Technology & Innovation',
                'summary' => 'Positioning technology ventures and sovereigns at the intersection of innovation and policy.',
                'motif' => 'cpu',
                'position' => 5,
            ],
            [
                'slug' => 'finance-investments',
                'name' => 'Finance & Investments',
                'summary' => 'Aligning capital allocators with the political and strategic context their investments depend on.',
                'motif' => 'coins',
                'position' => 6,
            ],
        ];

        foreach ($sectors as $sector) {
            SectorRecord::query()->updateOrCreate(
                ['slug' => $sector['slug']],
                [
                    'name' => $sector['name'],
                    'summary' => $sector['summary'],
                    'motif' => $sector['motif'],
                    'position' => $sector['position'],
                ],
            );
        }
    }
}
