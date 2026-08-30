<?php

declare(strict_types=1);

namespace Tests\Unit\Engagement;

use Domain\Engagement\ValueObjects\InterestArea;
use Tests\TestCase;

final class InterestAreaTest extends TestCase
{
    public function test_label_returns_a_human_readable_string_for_every_case(): void
    {
        $this->assertSame('Government & Political Affairs', InterestArea::GovernmentPoliticalAffairs->label());
        $this->assertSame('Prefer not to say', InterestArea::Undisclosed->label());
        $this->assertSame('Crisis Management & Special Situations', InterestArea::CrisisSpecialSituations->label());
    }

    public function test_requires_partner_triage_is_true_only_for_crisis_and_undisclosed(): void
    {
        $this->assertTrue(InterestArea::CrisisSpecialSituations->requiresPartnerTriage());
        $this->assertTrue(InterestArea::Undisclosed->requiresPartnerTriage());
        $this->assertFalse(InterestArea::GovernmentPoliticalAffairs->requiresPartnerTriage());
        $this->assertFalse(InterestArea::InvestmentCapitalStrategy->requiresPartnerTriage());
    }
}
