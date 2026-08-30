<?php

declare(strict_types=1);

namespace Tests\Unit\Content;

use Domain\Content\Entities\Sector;
use Domain\Shared\ValueObjects\Slug;
use Tests\TestCase;

final class SectorTest extends TestCase
{
    public function test_name_lines_splits_on_the_ampersand(): void
    {
        $sector = new Sector(
            slug: Slug::fromString('oil-gas'),
            name: 'Oil & Gas',
            summary: 'Energy majors and national oil companies.',
            motif: 'skyline',
            position: 1,
        );

        $this->assertSame(['Oil &', 'Gas'], $sector->nameLines());
    }

    public function test_name_lines_returns_a_single_line_when_there_is_no_ampersand(): void
    {
        $sector = new Sector(
            slug: Slug::fromString('telecommunications'),
            name: 'Telecommunications',
            summary: 'Carriers and infrastructure operators.',
            motif: 'grid',
            position: 2,
        );

        $this->assertSame(['Telecommunications'], $sector->nameLines());
    }

    public function test_to_array_exposes_the_expected_shape(): void
    {
        $sector = new Sector(
            slug: Slug::fromString('mining-natural-resources'),
            name: 'Mining & Natural Resources',
            summary: 'Extractives and resource governance.',
            motif: 'ledger',
            position: 3,
        );

        $data = $sector->toArray();

        $this->assertSame('mining-natural-resources', $data['slug']);
        $this->assertSame(['Mining &', 'Natural Resources'], $data['name_lines']);
        $this->assertSame('ledger', $data['motif']);
        $this->assertSame(3, $data['position']);
    }
}
