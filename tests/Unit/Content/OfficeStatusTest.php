<?php

declare(strict_types=1);

namespace Tests\Unit\Content;

use Domain\Content\ValueObjects\OfficeStatus;
use Tests\TestCase;

final class OfficeStatusTest extends TestCase
{
    public function test_label_returns_a_human_readable_string_for_every_case(): void
    {
        $this->assertSame('Planned', OfficeStatus::Planned->label());
        $this->assertSame('Partner Coverage', OfficeStatus::PartnerCoverage->label());
        $this->assertSame('Representative Office', OfficeStatus::RepresentativeOffice->label());
        $this->assertSame('Active Office', OfficeStatus::ActiveOffice->label());
    }

    public function test_only_active_office_describes_a_directly_staffed_location(): void
    {
        foreach (OfficeStatus::cases() as $status) {
            $description = $status->description();

            $this->assertNotSame('', $description);

            if ($status !== OfficeStatus::ActiveOffice) {
                $this->assertStringNotContainsString('directly operated', $description);
            }
        }

        $this->assertStringContainsString('directly operated', OfficeStatus::ActiveOffice->description());
    }
}
