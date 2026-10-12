<?php

declare(strict_types=1);

namespace Tests\Feature\Consultation;

use Domain\Consultation\Entities\ConsultationRequest;
use Domain\Consultation\ValueObjects\BudgetRange;
use Domain\Consultation\ValueObjects\ConsultationReference;
use Domain\Consultation\ValueObjects\ConsultationSector;
use Domain\Consultation\ValueObjects\ConsultationStatus;
use Domain\Consultation\ValueObjects\MaturityStage;
use Domain\Shared\ValueObjects\EmailAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Repositories\EloquentConsultationRequestRepository;
use Tests\TestCase;

final class EloquentConsultationRequestRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_save_then_find_by_reference_round_trips_a_request(): void
    {
        $request = ConsultationRequest::submit(
            name: 'Jordan Achebe',
            organisation: 'Achebe Holdings',
            role: 'Managing Director',
            email: EmailAddress::fromString('jordan@achebe-holdings.example'),
            country: 'Nigeria',
            sector: ConsultationSector::FinanceInvestments,
            maturityStage: MaturityStage::Growth,
            projectDescription: 'We are exploring a strategic capital raise across two jurisdictions.',
            needDescription: 'We need help scoping which consultation format would suit us best.',
            budgetRange: BudgetRange::From100kTo500k,
            hasSupportingDocuments: true,
        );

        $repository = new EloquentConsultationRequestRepository;
        $repository->save($request);

        $found = $repository->findByReference($request->reference);

        $this->assertNotNull($found);
        $this->assertSame($request->reference->value, $found->reference->value);
        $this->assertSame('Jordan Achebe', $found->name);
        $this->assertSame('Achebe Holdings', $found->organisation);
        $this->assertSame('Managing Director', $found->role);
        $this->assertSame('jordan@achebe-holdings.example', $found->email->value);
        $this->assertSame(ConsultationSector::FinanceInvestments, $found->sector);
        $this->assertSame(MaturityStage::Growth, $found->maturityStage);
        $this->assertSame(BudgetRange::From100kTo500k, $found->budgetRange);
        $this->assertTrue($found->hasSupportingDocuments);
        $this->assertSame(ConsultationStatus::Submitted, $found->status());
    }

    public function test_save_persists_a_status_transition(): void
    {
        $request = ConsultationRequest::submit(
            name: 'Amara Diallo',
            organisation: null,
            role: null,
            email: EmailAddress::fromString('amara@diallo.example'),
            country: null,
            sector: ConsultationSector::GovernmentPublicSector,
            maturityStage: MaturityStage::Idea,
            projectDescription: 'A new public-private partnership we are scoping for next year.',
            needDescription: 'We need a sense of what a first conversation would look like.',
            budgetRange: BudgetRange::Undisclosed,
            hasSupportingDocuments: false,
        );

        $repository = new EloquentConsultationRequestRepository;
        $repository->save($request);

        $request->transitionTo(ConsultationStatus::UnderReview);
        $repository->save($request);

        $found = $repository->findByReference($request->reference);

        $this->assertNotNull($found);
        $this->assertSame(ConsultationStatus::UnderReview, $found->status());
    }

    public function test_find_by_reference_returns_null_when_missing(): void
    {
        $repository = new EloquentConsultationRequestRepository;

        $this->assertNull($repository->findByReference(
            ConsultationReference::fromString('UGC-2026-7KQ4XB'),
        ));
    }
}
