<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Infrastructure\Persistence\Eloquent\Models\EngagementModelRecord;

/**
 * Seeds the four ways a client can retain the firm, from the content brief.
 */
final class EngagementModelSeeder extends Seeder
{
    public function run(): void
    {
        $models = [
            [
                'slug' => 'strategic-advisory-retainers',
                'name' => 'Strategic Advisory Retainers',
                'summary' => 'Ongoing counsel for principals who need a trusted, discreet voice inside the room where decisions are made.',
                'icon' => 'handshake',
                'position' => 1,
            ],
            [
                'slug' => 'project-based-engagements',
                'name' => 'Project-Based Engagements',
                'summary' => 'Focused mandates scoped to a single transaction, negotiation, or strategic objective, delivered against a defined outcome.',
                'icon' => 'target',
                'position' => 2,
            ],
            [
                'slug' => 'government-affairs-lobbying',
                'name' => 'Government Affairs & Lobbying',
                'summary' => 'Direct representation before governments and regulators, backed by relationships built over decades.',
                'icon' => 'landmark',
                'position' => 3,
            ],
            [
                'slug' => 'crisis-management-special-situations',
                'name' => 'Crisis Management & Special Situations',
                'summary' => 'Rapid-response counsel when reputations, deals, or relationships are under acute pressure.',
                'icon' => 'shield-check',
                'position' => 4,
            ],
        ];

        foreach ($models as $model) {
            EngagementModelRecord::query()->updateOrCreate(
                ['slug' => $model['slug']],
                [
                    'name' => $model['name'],
                    'summary' => $model['summary'],
                    'icon' => $model['icon'],
                    'position' => $model['position'],
                ],
            );
        }
    }
}
