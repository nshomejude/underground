<?php

declare(strict_types=1);

namespace Tests\Unit\Content;

use Domain\Content\ValueObjects\EmploymentStatus;
use Tests\TestCase;

final class EmploymentStatusTest extends TestCase
{
    public function test_label_returns_a_human_readable_string_for_every_case(): void
    {
        $this->assertSame('Employee', EmploymentStatus::Employee->label());
        $this->assertSame('Independent', EmploymentStatus::Independent->label());
    }

    public function test_every_case_round_trips_through_its_backing_value(): void
    {
        foreach (EmploymentStatus::cases() as $case) {
            $this->assertSame($case, EmploymentStatus::from($case->value));
        }
    }
}
