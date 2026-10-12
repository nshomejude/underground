<?php

declare(strict_types=1);

namespace Tests\Unit\Content;

use Domain\Content\Entities\PortfolioEngagement;
use Domain\Shared\ValueObjects\Slug;
use Tests\TestCase;

final class PortfolioEngagementTest extends TestCase
{
    public function test_to_array_exposes_the_case_study_fields(): void
    {
        $engagement = new PortfolioEngagement(
            slug: Slug::fromString('a-case-study'),
            sector: 'Finance & Investments',
            title: 'A Case Study',
            summary: 'Context goes here.',
            outcome: 'A demonstrable result.',
            position: 1,
            realRole: 'Retained advisor.',
            deliverables: ['Strategy memo', 'Stakeholder plan'],
            period: '6 months',
            publicationAgreementNote: 'No publication agreement on file.',
        );

        $data = $engagement->toArray();

        $this->assertSame('Retained advisor.', $data['real_role']);
        $this->assertSame(['Strategy memo', 'Stakeholder plan'], $data['deliverables']);
        $this->assertSame('6 months', $data['period']);
        $this->assertSame('No publication agreement on file.', $data['publication_agreement_note']);
    }

    public function test_the_case_study_fields_default_to_empty_so_existing_portfolio_callers_keep_working(): void
    {
        $engagement = new PortfolioEngagement(
            slug: Slug::fromString('legacy-engagement'),
            sector: 'Finance & Investments',
            title: 'A Legacy Engagement',
            summary: 'Context.',
            outcome: 'Result.',
            position: 1,
        );

        $this->assertNull($engagement->realRole);
        $this->assertSame([], $engagement->deliverables);
        $this->assertNull($engagement->period);
        $this->assertNull($engagement->publicationAgreementNote);
    }
}
