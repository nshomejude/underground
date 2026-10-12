<?php

declare(strict_types=1);

namespace Tests\Unit\Consultation;

use Domain\Consultation\Entities\ConsultationRequest;
use Domain\Consultation\Exceptions\IllegalConsultationTransition;
use Domain\Consultation\ValueObjects\BudgetRange;
use Domain\Consultation\ValueObjects\ConsultationSector;
use Domain\Consultation\ValueObjects\ConsultationStatus;
use Domain\Consultation\ValueObjects\MaturityStage;
use Domain\Shared\Exceptions\DomainException;
use Domain\Shared\ValueObjects\EmailAddress;
use Tests\TestCase;

final class ConsultationRequestTest extends TestCase
{
    private function submit(array $overrides = []): ConsultationRequest
    {
        return ConsultationRequest::submit(
            name: $overrides['name'] ?? 'Jordan Achebe',
            organisation: $overrides['organisation'] ?? 'Achebe Holdings',
            role: $overrides['role'] ?? 'Managing Director',
            email: $overrides['email'] ?? EmailAddress::fromString('jordan@achebe-holdings.example'),
            country: $overrides['country'] ?? 'Nigeria',
            sector: $overrides['sector'] ?? ConsultationSector::FinanceInvestments,
            maturityStage: $overrides['maturityStage'] ?? MaturityStage::Growth,
            projectDescription: $overrides['projectDescription'] ?? 'We are exploring a strategic capital raise across two jurisdictions.',
            needDescription: $overrides['needDescription'] ?? 'We need help scoping which consultation format would suit us best.',
            budgetRange: $overrides['budgetRange'] ?? BudgetRange::From100kTo500k,
            hasSupportingDocuments: $overrides['hasSupportingDocuments'] ?? true,
        );
    }

    public function test_submit_creates_a_request_in_submitted_status(): void
    {
        $request = $this->submit();

        $this->assertSame(ConsultationStatus::Submitted, $request->status());
        $this->assertStringStartsWith('UGC-', $request->reference->value);
        $this->assertSame('Jordan Achebe', $request->name);
    }

    public function test_submit_trims_the_name_and_descriptions(): void
    {
        $request = $this->submit([
            'name' => '  Jordan Achebe  ',
            'projectDescription' => '  We are exploring a strategic capital raise.  ',
            'needDescription' => '  We need help scoping the right format.  ',
        ]);

        $this->assertSame('Jordan Achebe', $request->name);
        $this->assertSame('We are exploring a strategic capital raise.', $request->projectDescription);
        $this->assertSame('We need help scoping the right format.', $request->needDescription);
    }

    public function test_submit_converts_blank_optional_fields_to_null(): void
    {
        $request = $this->submit([
            'organisation' => '   ',
            'role' => '  ',
            'country' => '',
        ]);

        $this->assertNull($request->organisation);
        $this->assertNull($request->role);
        $this->assertNull($request->country);
    }

    public function test_submit_rejects_a_blank_name(): void
    {
        $this->expectException(DomainException::class);

        $this->submit(['name' => '   ']);
    }

    public function test_submit_rejects_a_project_description_below_the_minimum_length(): void
    {
        $this->expectException(DomainException::class);

        $this->submit(['projectDescription' => 'Too short.']);
    }

    public function test_submit_rejects_a_need_description_below_the_minimum_length(): void
    {
        $this->expectException(DomainException::class);

        $this->submit(['needDescription' => 'Too short.']);
    }

    public function test_transition_to_follows_the_allowed_status_machine(): void
    {
        $request = $this->submit();

        $request->transitionTo(ConsultationStatus::UnderReview);
        $this->assertSame(ConsultationStatus::UnderReview, $request->status());

        $request->transitionTo(ConsultationStatus::Qualified);
        $this->assertSame(ConsultationStatus::Qualified, $request->status());

        $request->transitionTo(ConsultationStatus::ProposalSent);
        $this->assertSame(ConsultationStatus::ProposalSent, $request->status());
    }

    public function test_transition_to_rejects_an_illegal_move(): void
    {
        $request = $this->submit();

        $this->expectException(IllegalConsultationTransition::class);

        $request->transitionTo(ConsultationStatus::Qualified);
    }

    public function test_to_array_exposes_the_expected_shape(): void
    {
        $request = $this->submit();

        $data = $request->toArray();

        $this->assertSame('Jordan Achebe', $data['name']);
        $this->assertSame('finance-investments', $data['sector']);
        $this->assertSame('Finance & Investments', $data['sector_label']);
        $this->assertSame('growth', $data['maturity_stage']);
        $this->assertSame('100k-500k', $data['budget_range']);
        $this->assertTrue($data['has_supporting_documents']);
        $this->assertSame('submitted', $data['status']);
        $this->assertSame('Submitted', $data['status_label']);
        $this->assertArrayHasKey('reference', $data);
        $this->assertArrayHasKey('submitted_at', $data);
    }
}
