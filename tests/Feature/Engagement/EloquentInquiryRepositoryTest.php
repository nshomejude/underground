<?php

declare(strict_types=1);

namespace Tests\Feature\Engagement;

use Domain\Engagement\Entities\ConfidentialInquiry;
use Domain\Engagement\ValueObjects\InquiryReference;
use Domain\Engagement\ValueObjects\InquiryStatus;
use Domain\Engagement\ValueObjects\InterestArea;
use Domain\Shared\ValueObjects\EmailAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Repositories\EloquentInquiryRepository;
use Tests\TestCase;

final class EloquentInquiryRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_save_then_find_by_reference_round_trips_an_inquiry(): void
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

        $repository = new EloquentInquiryRepository;
        $repository->save($inquiry);

        $found = $repository->findByReference($inquiry->reference);

        $this->assertNotNull($found);
        $this->assertTrue($found->reference->value === $inquiry->reference->value);
        $this->assertSame('Jordan Achebe', $found->name);
        $this->assertSame('Achebe Holdings', $found->organisation);
        $this->assertSame('jordan@achebe-holdings.example', $found->email->value);
        $this->assertSame(InterestArea::InvestmentCapitalStrategy, $found->interest);
        $this->assertSame(InquiryStatus::Received, $found->status());
    }

    public function test_save_persists_a_status_transition(): void
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

        $repository = new EloquentInquiryRepository;
        $repository->save($inquiry);

        $inquiry->transitionTo(InquiryStatus::UnderReview);
        $repository->save($inquiry);

        $found = $repository->findByReference($inquiry->reference);

        $this->assertNotNull($found);
        $this->assertSame(InquiryStatus::UnderReview, $found->status());
    }

    public function test_find_by_reference_returns_null_when_missing(): void
    {
        $repository = new EloquentInquiryRepository;

        $this->assertNull($repository->findByReference(
            InquiryReference::fromString('UG-2026-7KQ4XB'),
        ));
    }
}
