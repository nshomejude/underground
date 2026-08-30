<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Infrastructure\Persistence\Eloquent\Models\MetricRecord;

/**
 * Seeds the five proof points in the credibility bar, from the content brief.
 */
final class MetricSeeder extends Seeder
{
    public function run(): void
    {
        $metrics = [
            [
                'slug' => 'government-relationships',
                'value' => '250+',
                'label' => 'Government Relationships Worldwide',
                'icon' => 'globe',
                'position' => 1,
            ],
            [
                'slug' => 'successful-engagements',
                'value' => '150+',
                'label' => 'Successful Engagements Delivered',
                'icon' => 'handshake',
                'position' => 2,
            ],
            [
                'slug' => 'countries-of-operation',
                'value' => '75+',
                'label' => 'Countries & Territories of Operation',
                'icon' => 'flag',
                'position' => 3,
            ],
            [
                'slug' => 'capital-supported',
                'value' => '$20B+',
                'label' => 'In Projects & Investments Supported',
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
