<?php

declare(strict_types=1);

namespace Tests\Unit\Content;

use Domain\Content\ValueObjects\TeamRoleCategory;
use Tests\TestCase;

final class TeamRoleCategoryTest extends TestCase
{
    public function test_label_returns_a_human_readable_string_for_every_case(): void
    {
        $this->assertSame('Governance', TeamRoleCategory::Governance->label());
        $this->assertSame('Executive', TeamRoleCategory::Executive->label());
        $this->assertSame('Regional', TeamRoleCategory::Regional->label());
        $this->assertSame('Expertise', TeamRoleCategory::Expertise->label());
    }

    public function test_every_case_round_trips_through_its_backing_value(): void
    {
        foreach (TeamRoleCategory::cases() as $case) {
            $this->assertSame($case, TeamRoleCategory::from($case->value));
        }
    }
}
