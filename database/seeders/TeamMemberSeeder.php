<?php

declare(strict_types=1);

namespace Database\Seeders;

use Domain\Content\ValueObjects\EmploymentStatus;
use Domain\Content\ValueObjects\TeamRoleCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Infrastructure\Persistence\Eloquent\Models\TeamMemberRecord;

/**
 * Seeds the leadership bios shown on /team and /team/senior-leadership,
 * migrated from the previously hardcoded TeamController array.
 *
 * Every existing principal is mapped onto the brief's roleCategory split
 * (Governance / Executive / Regional / Expertise) using their existing,
 * already-published title — the founder is Executive, every "Partner, X"
 * principal is Expertise (Partner is an Expertise-tier title per the
 * brief). None of the six existing people are Board Members, Advisory
 * Board members, or hold a Regional title, so those groups are
 * deliberately left empty here rather than inventing someone to fill
 * them — see the Senior Leadership page's empty-state copy.
 *
 * languages is left as a single, non-fabricated default: every bio here
 * is published in English and nothing in the source material claims any
 * other working language for these six people, so inventing specific
 * fluencies would be the kind of unverifiable claim the client brief asks
 * us to avoid.
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
                'role_category' => TeamRoleCategory::Executive,
                'employment_status' => EmploymentStatus::Employee,
            ],
            [
                'name' => 'Naledi Okonjo-Reyes',
                'title' => 'Partner, Government & Political Affairs',
                'background' => 'Former senior ministerial adviser who now sits on the other side of the table for a smaller number of principals.',
                'icon' => 'landmark',
                'has_portrait' => false,
                'role_category' => TeamRoleCategory::Expertise,
                'employment_status' => EmploymentStatus::Employee,
            ],
            [
                'name' => 'Marcus Thane',
                'title' => 'Partner, Strategic Intelligence & Analysis',
                'background' => 'Built and ran research desks for two multinational institutions before joining the firm at its founding.',
                'icon' => 'radar',
                'has_portrait' => false,
                'role_category' => TeamRoleCategory::Expertise,
                'employment_status' => EmploymentStatus::Employee,
            ],
            [
                'name' => 'Yumi Castellanos',
                'title' => 'Partner, Investment & Capital Strategy',
                'background' => 'Structured cross-border capital for sovereign and institutional allocators across three continents.',
                'icon' => 'coins',
                'has_portrait' => false,
                'role_category' => TeamRoleCategory::Expertise,
                'employment_status' => EmploymentStatus::Employee,
            ],
            [
                'name' => 'Elias Farrow',
                'title' => 'Partner, Crisis & Special Situations',
                'background' => 'Called in when a mandate has already gone public, his job is to make sure it does not stay that way.',
                'icon' => 'shield-check',
                'has_portrait' => false,
                'role_category' => TeamRoleCategory::Expertise,
                'employment_status' => EmploymentStatus::Employee,
            ],
            [
                'name' => 'Priya Anand-Whitfield',
                'title' => 'Partner, Media & Narrative Management',
                'background' => 'Two decades shaping how institutions are understood by the audiences that decide their fate.',
                'icon' => 'megaphone',
                'has_portrait' => false,
                'role_category' => TeamRoleCategory::Expertise,
                'employment_status' => EmploymentStatus::Employee,
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
                    'role_category' => $member['role_category']->value,
                    'employment_status' => $member['employment_status']->value,
                    'languages' => ['English'],
                    'position' => $position + 1,
                ],
            );
        }
    }
}
