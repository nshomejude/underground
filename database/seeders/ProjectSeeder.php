<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Infrastructure\Persistence\Eloquent\Models\ProjectRecord;

/**
 * Seeds the initiatives shown on /projects, migrated from the previously
 * hardcoded ProjectController array.
 */
final class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [
                'icon' => 'landmark',
                'title' => 'Ministerial Transition Advisory Program',
                'sector' => 'Government & Public Sector',
                'body' => 'A standing advisory program supporting three governments through post-election transitions, from continuity of policy through to institutional handover.',
            ],
            [
                'icon' => 'gem',
                'title' => 'Transition-Finance Working Group',
                'sector' => 'Energy & Natural Resources',
                'body' => 'A multi-year initiative convening lenders, regulators, and resource-holding states around a shared framework for financing the energy transition.',
            ],
            [
                'icon' => 'radar',
                'title' => 'Sovereign Technology Governance Initiative',
                'sector' => 'Technology & Innovation',
                'body' => 'Ongoing counsel to a cohort of sovereign technology funds preparing for the next wave of cross-border data and platform regulation.',
            ],
            [
                'icon' => 'ship-wheel',
                'title' => 'Strategic Corridor Infrastructure Review',
                'sector' => 'Infrastructure & Transportation',
                'body' => 'A live review of concession terms across a strategic trade corridor, run jointly with the operators and authorities who depend on it.',
            ],
            [
                'icon' => 'coins',
                'title' => 'Institutional Capital Realignment Program',
                'sector' => 'Finance & Investments',
                'body' => 'Advising a cohort of institutional allocators through a multi-year realignment of exposure across contested and emerging markets.',
            ],
        ];

        foreach ($projects as $position => $project) {
            ProjectRecord::query()->updateOrCreate(
                ['slug' => Str::slug($project['title'])],
                [
                    'icon' => $project['icon'],
                    'title' => $project['title'],
                    'sector' => $project['sector'],
                    'body' => $project['body'],
                    'position' => $position + 1,
                ],
            );
        }
    }
}
