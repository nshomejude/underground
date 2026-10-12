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
            slug: Slug::fromString('government-affairs-institutional-relations'),
            title: 'Government Affairs & Institutional Relations',
            summary: 'Discreet counsel at the intersection of policy and power.',
            icon: 'landmark',
            position: 1,
            isFeatured: true,
            problemStatement: 'Governments move on their own timeline.',
            audiences: ['Ministers and senior officials'],
            method: 'We map authority before we arrange a meeting.',
            deliverables: ['A stakeholder map'],
            confirmedLead: null,
            authorizedReference: null,
            nextAction: 'Start a confidential inquiry',
        );

        $this->assertSame(
            ['Government Affairs &', 'Institutional Relations'],
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
            problemStatement: 'A situation has already gone public.',
            audiences: ['Leaders in an active crisis'],
            method: 'We stabilize first, then resolve.',
            deliverables: ['A rapid situation assessment'],
            confirmedLead: null,
            authorizedReference: null,
            nextAction: 'Start a confidential inquiry',
        );

        $this->assertSame(['Crisis Management'], $capability->titleLines());
    }

    public function test_to_array_exposes_the_expected_shape(): void
    {
        $capability = new Capability(
            slug: Slug::fromString('strategic-intelligence-research'),
            title: 'Strategic Intelligence & Research',
            summary: 'Rigorous, actionable insight for high-stakes decisions.',
            icon: 'radar',
            position: 3,
            isFeatured: true,
            problemStatement: 'Decisions get made on incomplete information.',
            audiences: ['Boards weighing a market-entry decision'],
            method: 'We work only from lawful, ethical sources.',
            deliverables: ['A structured risk assessment'],
            confirmedLead: null,
            authorizedReference: null,
            nextAction: 'Start a confidential inquiry',
        );

        $data = $capability->toArray();

        $this->assertSame('strategic-intelligence-research', $data['slug']);
        $this->assertSame('Strategic Intelligence & Research', $data['title']);
        $this->assertSame(['Strategic Intelligence &', 'Research'], $data['title_lines']);
        $this->assertSame('radar', $data['icon']);
        $this->assertSame(3, $data['position']);
        $this->assertTrue($data['featured']);
        $this->assertSame('Decisions get made on incomplete information.', $data['problem_statement']);
        $this->assertSame(['Boards weighing a market-entry decision'], $data['audiences']);
        $this->assertSame('We work only from lawful, ethical sources.', $data['method']);
        $this->assertSame(['A structured risk assessment'], $data['deliverables']);
        $this->assertNull($data['confirmed_lead']);
        $this->assertNull($data['authorized_reference']);
        $this->assertSame('Start a confidential inquiry', $data['next_action']);
    }
}
