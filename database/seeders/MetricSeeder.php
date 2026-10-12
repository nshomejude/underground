<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Infrastructure\Persistence\Eloquent\Models\MetricRecord;

/**
 * Seeds the five proof points in the credibility bar. Deliberately
 * qualitative rather than specific numeric claims (e.g. "250+", "$20B+")
 * -- the client brief instructs that unverifiable performance figures
 * presented as settled fact be verified or removed, and no real backing
 * data exists for precise counts here, so these read as honest
 * positioning language instead of fabricated precision.
 */
final class MetricSeeder extends Seeder
{
    public function run(): void
    {
        $metrics = [
            [
                'slug' => 'government-relationships',
                'value' => 'Global',
                'label' => 'Government Relationships Network',
                'icon' => 'globe',
                'position' => 1,
            ],
            [
                'slug' => 'successful-engagements',
                'value' => 'Proven',
                'label' => 'Track Record of Engagements',
                'icon' => 'handshake',
                'position' => 2,
            ],
            [
                'slug' => 'countries-of-operation',
                'value' => 'Worldwide',
                'label' => 'Countries & Territories Served',
                'icon' => 'flag',
                'position' => 3,
            ],
            [
                'slug' => 'capital-supported',
                'value' => 'Strategic',
                'label' => 'Capital & Investment Support',
                'icon' => 'landmark',
                'position' => 4,
            ],
            [
                'slug' => 'discretion',
                'value' => '100%',
                'label' => 'Discretion. Loyalty. Results.',
                'icon' => 'shield-check',
                'position' => 5,
            ],
        ];

        foreach ($metrics as $metric) {
            MetricRecord::query()->updateOrCreate(
                ['slug' => $metric['slug']],
                [
                    'value' => $metric['value'],
                    'label' => $metric['label'],
                    'icon' => $metric['icon'],
                    'position' => $metric['position'],
                ],
            );
        }
    }
}
