<?php

declare(strict_types=1);

namespace Tests\Unit\Content;

use Domain\Content\Entities\Capability;
use Domain\Shared\ValueObjects\Slug;
use Tests\TestCase;

final class CapabilityTest extends TestCase
{
    public function test_title_lines_splits_on_the_ampersand(): void
    {
        $capability = new Capability(
            slug: Slug::fromString('government-political-affairs'),
            title: 'Government & Political Affairs',
            summary: 'Discreet counsel at the intersection of policy and power.',
            icon: 'landmark',
            position: 1,
            isFeatured: true,
        );

        $this->assertSame(
            ['Government &', 'Political Affairs'],
            $capability->titleLines(),
        );
    }

    public function test_title_lines_returns_a_single_line_when_there_is_no_ampersand(): void
    {
        $capability = new Capability(
            slug: Slug::fromString('crisis-management'),
            title: 'Crisis Management',
            summary: 'Rapid, discreet response to special situations.',
            icon: 'shield',
            position: 2,
            isFeatured: false,
        );

        $this->assertSame(['Crisis Management'], $capability->titleLines());
    }

    public function test_to_array_exposes_the_expected_shape(): void
    {
        $capability = new Capability(
            slug: Slug::fromString('strategic-intelligence-analysis'),
            title: 'Strategic Intelligence & Analysis',
            summary: 'Rigorous, actionable insight for high-stakes decisions.',
            icon: 'radar',
            position: 3,
            isFeatured: true,
        );

        $data = $capability->toArray();

        $this->assertSame('strategic-intelligence-analysis', $data['slug']);
        $this->assertSame('Strategic Intelligence & Analysis', $data['title']);
        $this->assertSame(['Strategic Intelligence &', 'Analysis'], $data['title_lines']);
        $this->assertSame('radar', $data['icon']);
        $this->assertSame(3, $data['position']);
        $this->assertTrue($data['featured']);
    }
}
