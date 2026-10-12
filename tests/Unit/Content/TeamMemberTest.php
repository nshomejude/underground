<?php

declare(strict_types=1);

namespace Tests\Unit\Content;

use Domain\Content\Entities\TeamMember;
use Domain\Content\ValueObjects\EmploymentStatus;
use Domain\Content\ValueObjects\TeamRoleCategory;
use Domain\Shared\ValueObjects\Slug;
use Tests\TestCase;

final class TeamMemberTest extends TestCase
{
    public function test_to_array_exposes_the_expected_shape(): void
    {
        $member = new TeamMember(
            slug: Slug::fromString('tony-smith'),
            name: 'Tony Smith',
            title: 'Founder & Managing Partner',
            background: 'Two decades brokering quiet agreements.',
            icon: null,
            hasPortrait: true,
            roleCategory: TeamRoleCategory::Executive,
            employmentStatus: EmploymentStatus::Employee,
            languages: ['English'],
            position: 1,
        );

        $data = $member->toArray();

        $this->assertSame('tony-smith', $data['slug']);
        $this->assertSame('executive', $data['role_category']);
        $this->assertSame('Executive', $data['role_category_label']);
        $this->assertSame('employee', $data['employment_status']);
        $this->assertSame('Employee', $data['employment_status_label']);
        $this->assertSame(['English'], $data['languages']);
    }

    public function test_a_governance_member_titled_board_member_is_a_board_member_not_advisory(): void
    {
        $member = $this->governanceMember('Board Member');

        $this->assertTrue($member->isBoardMember());
        $this->assertFalse($member->isAdvisoryBoard());
    }

    public function test_a_governance_member_titled_advisory_board_is_advisory_not_a_board_member(): void
    {
        $member = $this->governanceMember('Advisory Board Member');

        $this->assertTrue($member->isAdvisoryBoard());
        $this->assertFalse($member->isBoardMember());
    }

    public function test_a_non_governance_member_is_neither(): void
    {
        $member = new TeamMember(
            slug: Slug::fromString('a-partner'),
            name: 'A Partner',
            title: 'Partner, Something',
            background: 'Background.',
            icon: 'target',
            hasPortrait: false,
            roleCategory: TeamRoleCategory::Expertise,
            employmentStatus: EmploymentStatus::Employee,
            languages: [],
            position: 2,
        );

        $this->assertFalse($member->isBoardMember());
        $this->assertFalse($member->isAdvisoryBoard());
    }

    private function governanceMember(string $title): TeamMember
    {
        return new TeamMember(
            slug: Slug::fromString('a-governor'),
            name: 'A Governor',
            title: $title,
            background: 'Background.',
            icon: 'users',
            hasPortrait: false,
            roleCategory: TeamRoleCategory::Governance,
            employmentStatus: EmploymentStatus::Independent,
            languages: [],
            position: 3,
        );
    }
}
