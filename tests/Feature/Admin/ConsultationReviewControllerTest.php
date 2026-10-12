<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Application\Consultation\Actions\SubmitConsultationRequest;
use Application\Consultation\DataTransferObjects\ConsultationRequestPayload;
use Application\Consultation\Queries\ListConsultationRequests;
use Domain\Consultation\Entities\ConsultationRequest;
use Domain\Consultation\ValueObjects\BudgetRange;
use Domain\Consultation\ValueObjects\ConsultationSector;
use Domain\Consultation\ValueObjects\ConsultationStatus;
use Domain\Consultation\ValueObjects\MaturityStage;
use Domain\Shared\ValueObjects\EmailAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ConsultationReviewControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true]);
    }

    private function submitRequest(string $email = 'prospect@example.com'): ConsultationRequest
    {
        return ($this->app->make(SubmitConsultationRequest::class))(new ConsultationRequestPayload(
            name: 'Jordan Achebe',
            organisation: null,
            role: null,
            email: EmailAddress::fromString($email),
            country: null,
            sector: ConsultationSector::TechnologyInnovation,
            maturityStage: MaturityStage::EarlyStage,
            projectDescription: 'Seeking a discreet introduction to explore a long-term advisory relationship.',
            needDescription: 'We need a sense of what the right consultation format would be for us.',
            budgetRange: BudgetRange::Undisclosed,
            hasSupportingDocuments: false,
        ));
    }

    private function findRequest(string $reference): ConsultationRequest
    {
        $all = ($this->app->make(ListConsultationRequests::class))();

        return collect($all)->first(fn (ConsultationRequest $r) => $r->reference->value === $reference);
    }

    public function test_the_index_lists_requests_with_status_badges(): void
    {
        $request = $this->submitRequest();

        $response = $this->actingAs($this->admin)->get(route('admin.consultations.index'));

        $response->assertOk();
        $response->assertSee($request->reference->value);
        $response->assertSee($request->name);
        $response->assertSee('Submitted');
    }

    public function test_the_show_page_renders_the_full_request_detail(): void
    {
        $request = $this->submitRequest();

        $response = $this->actingAs($this->admin)->get(route('admin.consultations.show', $request->reference->value));

        $response->assertOk();
        $response->assertSee($request->reference->value);
        $response->assertSee($request->name);
        $response->assertSee($request->projectDescription);
        $response->assertSee($request->needDescription);
    }

    public function test_a_non_admin_cannot_view_the_show_page(): void
    {
        $request = $this->submitRequest();
        $member = User::factory()->create(['is_admin' => false]);

        $this->actingAs($member)->get(route('admin.consultations.show', $request->reference->value))->assertForbidden();
    }

    public function test_a_legal_transition_succeeds(): void
    {
        $request = $this->submitRequest('legal@example.com');

        $response = $this->actingAs($this->admin)->post(
            route('admin.consultations.transition', $request->reference->value),
            ['status' => ConsultationStatus::UnderReview->value],
        );

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $updated = $this->findRequest($request->reference->value);
        $this->assertSame(ConsultationStatus::UnderReview, $updated->status());
    }

    public function test_an_illegal_transition_is_rejected_with_a_flash_error_not_a_500(): void
    {
        $request = $this->submitRequest('illegal@example.com');

        // Submitted -> Qualified is not a legal move; must go through UnderReview.
        $response = $this->actingAs($this->admin)->post(
            route('admin.consultations.transition', $request->reference->value),
            ['status' => ConsultationStatus::Qualified->value],
        );

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $updated = $this->findRequest($request->reference->value);
        $this->assertSame(ConsultationStatus::Submitted, $updated->status());
    }

    public function test_a_non_admin_cannot_transition_a_request(): void
    {
        $request = $this->submitRequest('blocked@example.com');
        $member = User::factory()->create(['is_admin' => false]);

        $this->actingAs($member)->post(
            route('admin.consultations.transition', $request->reference->value),
            ['status' => ConsultationStatus::UnderReview->value],
        )->assertForbidden();
    }
}
