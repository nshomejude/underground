<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class InquiryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_endpoint_accepts_a_valid_inquiry(): void
    {
        $response = $this->postJson('/api/v1/inquiries', $this->validPayload());

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Jordan Achebe')
            ->assertJsonPath('data.email', 'jordan@achebe-holdings.example')
            ->assertJsonPath('data.interest', 'investment-capital-strategy')
            ->assertJsonPath('data.status', 'received')
            ->assertJsonStructure([
                'data' => [
                    'reference', 'name', 'organisation', 'email', 'phone',
                    'country', 'interest', 'interest_label', 'brief',
                    'status', 'status_label', 'partner_triage', 'submitted_at',
                ],
            ]);

        $this->assertMatchesRegularExpression(
            '/^UG-\d{4}-[A-HJ-NP-Z2-9]{6}$/',
            $response->json('data.reference'),
        );
    }

    public function test_store_endpoint_rejects_an_invalid_submission(): void
    {
        $response = $this->postJson('/api/v1/inquiries', [
            'name' => 'Jordan Achebe',
            'email' => 'not-an-email',
            'interest' => 'not-a-real-interest',
            'brief' => 'too short',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['email', 'interest', 'brief']);
    }

    public function test_show_endpoint_returns_404_for_an_unknown_reference(): void
    {
        $response = $this->getJson('/api/v1/inquiries/UG-2026-ZZZZZZ');

        $response->assertStatus(404);
    }

    public function test_store_endpoint_is_throttled(): void
    {
        $middleware = collect(app('router')->getRoutes())
            ->first(fn ($route) => $route->getName() === 'api.v1.inquiries.store')
            ->middleware();

        $this->assertContains('throttle:10,1', $middleware);
    }

    public function test_a_submitted_inquiry_can_be_tracked_end_to_end(): void
    {
        $submitted = $this->postJson('/api/v1/inquiries', $this->validPayload());
        $submitted->assertCreated();

        $reference = $submitted->json('data.reference');

        $tracked = $this->getJson("/api/v1/inquiries/{$reference}");

        $tracked->assertOk();

        $this->assertSame($submitted->json('data'), $tracked->json('data'));
    }

    /** @return array<string, mixed> */
    private function validPayload(): array
    {
        return [
            'name' => 'Jordan Achebe',
            'organisation' => 'Achebe Holdings',
            'email' => 'jordan@achebe-holdings.example',
            'phone' => '+1 202 555 0100',
            'country' => 'Nigeria',
            'interest' => 'investment-capital-strategy',
            'brief' => 'We are exploring a strategic capital raise across two jurisdictions.',
        ];
    }
}
