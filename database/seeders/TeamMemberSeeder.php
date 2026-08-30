<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Infrastructure\Persistence\Eloquent\Models\TeamMemberRecord;

/**
 * Seeds the leadership bios shown on /team, migrated from the previously
 * hardcoded TeamController array.
 */
final class TeamMemberSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            [
                'name' => 'Tony Smith',
                'title' => 'Founder & Managing Partner',
                'background' => 'Two decades brokering quiet agreements between sovereigns, capital, and the institutions caught between them.',
                'icon' => null,
                'has_portrait' => true,
            ],
            [
                'name' => 'Naledi Okonjo-Reyes',
                'title' => 'Partner, Government & Political Affairs',
                'background' => 'Former senior ministerial adviser who now sits on the other side of the table for a smaller number of principals.',
                'icon' => 'landmark',
                'has_portrait' => false,
            ],
            [
                'name' => 'Marcus Thane',
                'title' => 'Partner, Strategic Intelligence & Analysis',
                'background' => 'Built and ran research desks for two multinational institutions before joining the firm at its founding.',
                'icon' => 'radar',
                'has_portrait' => false,
            ],
            [
                'name' => 'Yumi Castellanos',
                'title' => 'Partner, Investment & Capital Strategy',
                'background' => 'Structured cross-border capital for sovereign and institutional allocators across three continents.',
                'icon' => 'coins',
                'has_portrait' => false,
            ],
            [
                'name' => 'Elias Farrow',
                'title' => 'Partner, Crisis & Special Situations',
                'background' => 'Called in when a mandate has already gone public, his job is to make sure it does not stay that way.',
                'icon' => 'shield-check',
                'has_portrait' => false,
            ],
            [
                'name' => 'Priya Anand-Whitfield',
                'title' => 'Partner, Media & Narrative Management',
                'background' => 'Two decades shaping how institutions are understood by the audiences that decide their fate.',
                'icon' => 'megaphone',
                'has_portrait' => false,
            ],
        ];

        foreach ($members as $position => $member) {
            TeamMemberRecord::query()->updateOrCreate(
                ['slug' => Str::slug($member['name'])],
                [
                    'name' => $member['name'],
                    'title' => $member['title'],
                    'background' => $member['background'],
                    'icon' => $member['icon'],
                    'has_portrait' => $member['has_portrait'],
                    'position' => $position + 1,
                ],
            );
        }
    }
}
