<?php

declare(strict_types=1);

namespace Tests\Feature\Consultation;

use Application\Consultation\Actions\SubmitConsultationRequest;
use Application\Consultation\Actions\TransitionConsultationStatus;
use Application\Consultation\DataTransferObjects\ConsultationRequestPayload;
use Domain\Consultation\Entities\ConsultationRequest;
use Domain\Consultation\ValueObjects\BudgetRange;
use Domain\Consultation\ValueObjects\ConsultationSector;
use Domain\Consultation\ValueObjects\ConsultationStatus;
use Domain\Consultation\ValueObjects\MaturityStage;
use Domain\Shared\ValueObjects\EmailAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ConsultationTrackControllerTest extends TestCase
{
    use RefreshDatabase;

    private function submitRequest(): ConsultationRequest
    {
        return ($this->app->make(SubmitConsultationRequest::class))(new ConsultationRequestPayload(
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
        ));
    }

    public function test_the_track_page_renders_with_no_reference_supplied(): void
    {
        $this->get(route('consultations.track'))
            ->assertOk()
            ->assertSee('Track Your Request');
    }

    public function test_a_valid_reference_shows_the_correct_status(): void
    {
        $request = $this->submitRequest();

        ($this->app->make(TransitionConsultationStatus::class))(
            $request->reference->value,
            ConsultationStatus::UnderReview,
        );

        $response = $this->get(route('consultations.track', ['reference' => $request->reference->value]));

        $response->assertOk();
        $response->assertSee($request->reference->value);
        $response->assertSee('Under review');
        $response->assertSee('Finance & Investments');
        $response->assertSee($request->submittedAt->format('j M Y'));
    }

    public function test_the_public_view_never_renders_private_details(): void
    {
        $request = $this->submitRequest();

        $response = $this->get(route('consultations.track', ['reference' => $request->reference->value]));

        $response->assertOk();
        $response->assertDontSee($request->name);
        $response->assertDontSee($request->organisation);
        $response->assertDontSee($request->email->value);
        $response->assertDontSee($request->projectDescription);
        $response->assertDontSee($request->needDescription);
    }

    public function test_an_unknown_but_well_formed_reference_shows_a_friendly_message(): void
    {
        $response = $this->get(route('consultations.track', ['reference' => 'UGC-2026-ZZZZZZ']));

        $response->assertOk();
        $response->assertSee('couldn&rsquo;t find a consultation request', false);
    }

    public function test_a_malformed_reference_shows_a_friendly_message_not_a_500(): void
    {
        $response = $this->get(route('consultations.track', ['reference' => 'not-a-real-reference']));

        $response->assertOk();
        $response->assertSee('couldn&rsquo;t find a consultation request', false);
    }
}
