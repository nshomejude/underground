<?php

declare(strict_types=1);

namespace Tests\Unit\Engagement;

use Domain\Engagement\Entities\ConfidentialInquiry;
use Domain\Engagement\Exceptions\IllegalInquiryTransition;
use Domain\Engagement\ValueObjects\InquiryStatus;
use Domain\Engagement\ValueObjects\InterestArea;
use Domain\Shared\Exceptions\DomainException;
use Domain\Shared\ValueObjects\EmailAddress;
use Tests\TestCase;

final class ConfidentialInquiryTest extends TestCase
{
    public function test_submit_creates_an_inquiry_in_received_status(): void
    {
        $inquiry = ConfidentialInquiry::submit(
            name: 'Jordan Achebe',
            organisation: 'Achebe Holdings',
            email: EmailAddress::fromString('jordan@achebe-holdings.example'),
            phone: '+1 202 555 0100',
            country: 'Nigeria',
            interest: InterestArea::InvestmentCapitalStrategy,
            brief: 'We are exploring a strategic capital raise across two jurisdictions.',
        );

        $this->assertSame(InquiryStatus::Received, $inquiry->status());
        $this->assertStringStartsWith('UG-', $inquiry->reference->value);
        $this->assertSame('Jordan Achebe', $inquiry->name);
    }

    public function test_submit_trims_the_name_and_brief(): void
    {
        $inquiry = ConfidentialInquiry::submit(
            name: '  Jordan Achebe  ',
            organisation: null,
            email: EmailAddress::fromString('jordan@achebe-holdings.example'),
            phone: null,
            country: null,
            interest: InterestArea::Undisclosed,
            brief: '  We are exploring a strategic capital raise across jurisdictions.  ',
        );

        $this->assertSame('Jordan Achebe', $inquiry->name);
        $this->assertSame('We are exploring a strategic capital raise across jurisdictions.', $inquiry->brief);
    }

    public function test_submit_converts_blank_optional_fields_to_null(): void
    {
        $inquiry = ConfidentialInquiry::submit(
            name: 'Jordan Achebe',
            organisation: '   ',
            email: EmailAddress::fromString('jordan@achebe-holdings.example'),
            phone: '  ',
            country: '',
            interest: InterestArea::Undisclosed,
            brief: 'We are exploring a strategic capital raise across jurisdictions.',
        );

        $this->assertNull($inquiry->organisation);
        $this->assertNull($inquiry->phone);
        $this->assertNull($inquiry->country);
    }

    public function test_submit_rejects_a_blank_name(): void
    {
        $this->expectException(DomainException::class);

        ConfidentialInquiry::submit(
            name: '   ',
            organisation: null,
            email: EmailAddress::fromString('jordan@achebe-holdings.example'),
            phone: null,
            country: null,
            interest: InterestArea::Undisclosed,
            brief: 'We are exploring a strategic capital raise across jurisdictions.',
        );
    }

    public function test_submit_rejects_a_brief_below_the_minimum_length(): void
    {
        $this->expectException(DomainException::class);

        ConfidentialInquiry::submit(
            name: 'Jordan Achebe',
            organisation: null,
            email: EmailAddress::fromString('jordan@achebe-holdings.example'),
            phone: null,
            country: null,
            interest: InterestArea::Undisclosed,
            brief: 'Too short.',
        );
    }

    public function test_transition_to_follows_the_allowed_status_machine(): void
    {
        $inquiry = ConfidentialInquiry::submit(
            name: 'Amara Diallo',
            organisation: null,
            email: EmailAddress::fromString('amara@diallo.example'),
            phone: null,
            country: null,
            interest: InterestArea::GovernmentPoliticalAffairs,
            brief: 'Seeking counsel on a cross-border regulatory matter.',
        );

        $inquiry->transitionTo(InquiryStatus::UnderReview);
        $this->assertSame(InquiryStatus::UnderReview, $inquiry->status());

        $inquiry->transitionTo(InquiryStatus::Engaged);
        $this->assertSame(InquiryStatus::Engaged, $inquiry->status());
    }

    public function test_transition_to_rejects_an_illegal_move(): void
    {
        $inquiry = ConfidentialInquiry::submit(
            name: 'Amara Diallo',
            organisation: null,
            email: EmailAddress::fromString('amara@diallo.example'),
            phone: null,
            country: null,
            interest: InterestArea::GovernmentPoliticalAffairs,
            brief: 'Seeking counsel on a cross-border regulatory matter.',
        );

        $this->expectException(IllegalInquiryTransition::class);

        $inquiry->transitionTo(InquiryStatus::Engaged);
    }

    public function test_needs_partner_triage_is_true_for_crisis_interest(): void
    {
        $inquiry = ConfidentialInquiry::submit(
            name: 'Amara Diallo',
            organisation: null,
            email: EmailAddress::fromString('amara@diallo.example'),
            phone: null,
            country: null,
            interest: InterestArea::CrisisSpecialSituations,
            brief: 'A time-sensitive special situation requiring immediate discretion.',
        );

        $this->assertTrue($inquiry->needsPartnerTriage());
    }

    public function test_needs_partner_triage_is_true_for_gov_and_mil_domains(): void
    {
        $govInquiry = ConfidentialInquiry::submit(
            name: 'Amara Diallo',
            organisation: null,
            email: EmailAddress::fromString('amara@state.gov'),
            phone: null,
            country: null,
            interest: InterestArea::InvestmentCapitalStrategy,
            brief: 'A sovereign infrastructure financing matter for review.',
        );

        $milInquiry = ConfidentialInquiry::submit(
            name: 'Amara Diallo',
            organisation: null,
            email: EmailAddress::fromString('amara@defense.mil'),
            phone: null,
            country: null,
            interest: InterestArea::InvestmentCapitalStrategy,
            brief: 'A sovereign infrastructure financing matter for review.',
        );

        $this->assertTrue($govInquiry->needsPartnerTriage());
        $this->assertTrue($milInquiry->needsPartnerTriage());
    }

    public function test_needs_partner_triage_is_false_for_an_ordinary_inquiry(): void
    {
        $inquiry = ConfidentialInquiry::submit(
            name: 'Amara Diallo',
            organisation: null,
            email: EmailAddress::fromString('amara@diallo.example'),
            phone: null,
            country: null,
            interest: InterestArea::BusinessDevelopment,
            brief: 'Looking to explore a business development partnership.',
        );

        $this->assertFalse($inquiry->needsPartnerTriage());
    }

    public function test_to_array_exposes_the_expected_shape(): void
    {
        $inquiry = ConfidentialInquiry::submit(
            name: 'Jordan Achebe',
            organisation: 'Achebe Holdings',
            email: EmailAddress::fromString('jordan@achebe-holdings.example'),
            phone: '+1 202 555 0100',
            country: 'Nigeria',
            interest: InterestArea::InvestmentCapitalStrategy,
            brief: 'We are exploring a strategic capital raise across two jurisdictions.',
        );

        $data = $inquiry->toArray();

        $this->assertSame('Jordan Achebe', $data['name']);
        $this->assertSame('investment-capital-strategy', $data['interest']);
        $this->assertSame('Investment & Capital Strategy', $data['interest_label']);
        $this->assertSame('received', $data['status']);
        $this->assertSame('Received', $data['status_label']);
        $this->assertFalse($data['partner_triage']);
        $this->assertArrayHasKey('reference', $data);
        $this->assertArrayHasKey('submitted_at', $data);
    }
}
