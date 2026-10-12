<?php

declare(strict_types=1);

namespace Tests\Feature\Consultation;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Models\ConsultationRequestRecord;
use Tests\TestCase;

final class ConsultationControllerTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Jordan Achebe',
            'organisation' => 'Achebe Holdings',
            'role' => 'Managing Director',
            'email' => 'jordan@achebe-holdings.example',
            'country' => 'Nigeria',
            'sector' => 'finance-investments',
            'maturity_stage' => 'growth',
            'project_description' => 'We are exploring a strategic capital raise across two jurisdictions.',
            'need_description' => 'We need help scoping which consultation format would suit us best.',
            'budget_range' => '100k-500k',
            'has_supporting_documents' => '1',
        ], $overrides);
    }

    public function test_the_form_page_renders(): void
    {
        $this->get(route('consultations.create'))
            ->assertOk()
            ->assertSee('Underground Consultation Desk');
    }

    public function test_a_valid_submission_creates_a_request_and_shows_its_reference(): void
    {
        $response = $this->from(route('consultations.create'))->post(route('consultations.store'), $this->validPayload());

        $response->assertRedirect(route('consultations.create'));
        $response->assertSessionHas('reference');

        $this->assertDatabaseCount('consultation_requests', 1);

        $record = ConsultationRequestRecord::query()->first();
        $this->assertSame('Jordan Achebe', $record->name);
        $this->assertSame('jordan@achebe-holdings.example', $record->email);
        $this->assertSame('finance-investments', $record->sector);
        $this->assertSame('submitted', $record->status);
        $this->assertTrue((bool) $record->has_supporting_documents);

        $reference = $response->getSession()->get('reference');
        $this->assertSame($reference, $record->reference);
        $this->assertStringStartsWith('UGC-', $reference);

        $this->followRedirects($response)
            ->assertOk()
            ->assertSee('Request Received')
            ->assertSee($reference);
    }

    public function test_the_checkbox_defaults_to_false_when_omitted(): void
    {
        $payload = $this->validPayload();
        unset($payload['has_supporting_documents']);

        $this->post(route('consultations.store'), $payload);

        $record = ConsultationRequestRecord::query()->first();
        $this->assertFalse((bool) $record->has_supporting_documents);
    }

    public function test_an_invalid_submission_is_rejected_and_persists_nothing(): void
    {
        $response = $this->from(route('consultations.create'))->post(route('consultations.store'), [
            'name' => 'Jordan Achebe',
            'email' => 'not-an-email',
            'sector' => 'not-a-real-sector',
            'maturity_stage' => 'not-a-real-stage',
            'project_description' => 'too short',
            'need_description' => 'too short',
            'budget_range' => 'not-a-real-range',
        ]);

        $response->assertRedirect(route('consultations.create'));
        $response->assertSessionHasErrors(['email', 'sector', 'maturity_stage', 'project_description', 'need_description', 'budget_range']);

        $this->assertDatabaseCount('consultation_requests', 0);
    }

    public function test_a_description_that_is_too_short_is_rejected(): void
    {
        $response = $this->post(route('consultations.store'), $this->validPayload([
            'project_description' => 'far too short',
        ]));

        $response->assertSessionHasErrors('project_description');
        $this->assertDatabaseCount('consultation_requests', 0);
    }
}
