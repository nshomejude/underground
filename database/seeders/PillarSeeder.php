<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Infrastructure\Persistence\Eloquent\Models\PillarRecord;

/**
 * Seeds the four brand pillars from the content brief.
 */
final class PillarSeeder extends Seeder
{
    public function run(): void
    {
        $pillars = [
            [
                'slug' => 'discreet-by-design',
                'title' => 'Discreet by Design',
                'qualifier' => 'by Design',
                'icon' => 'shield-check',
                'position' => 1,
            ],
            [
                'slug' => 'strategic-by-nature',
                'title' => 'Strategic by Nature',
                'qualifier' => 'by Nature',
                'icon' => 'target',
                'position' => 2,
            ],
            [
                'slug' => 'effective-by-execution',
                'title' => 'Effective by Execution',
                'qualifier' => 'by Execution',
                'icon' => 'radar',
                'position' => 3,
            ],
            [
                'slug' => 'global-by-reach',
                'title' => 'Global by Reach',
                'qualifier' => 'by Reach',
                'icon' => 'globe',
                'position' => 4,
            ],
        ];

        foreach ($pillars as $pillar) {
            PillarRecord::query()->updateOrCreate(
                ['slug' => $pillar['slug']],
                [
                    'title' => $pillar['title'],
                    'qualifier' => $pillar['qualifier'],
                    'icon' => $pillar['icon'],
                    'position' => $pillar['position'],
                ],
            );
        }
    }
}
