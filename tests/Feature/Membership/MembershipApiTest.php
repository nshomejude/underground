<?php

declare(strict_types=1);

namespace Tests\Feature\Membership;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class MembershipApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_tiers_endpoint_lists_the_vetted_membership_tiers(): void
    {
        $response = $this->getJson('/api/v1/membership/tiers');

        $response->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('data.0.slug', 'sovereign-partner')
            ->assertJsonPath('data.1.slug', 'principal-circle')
            ->assertJsonPath('data.2.slug', 'corporate-affiliate')
            ->assertJsonStructure([
                'data' => [
                    ['slug', 'name', 'audience', 'icon', 'position'],
                ],
            ]);
    }

    public function test_apply_endpoint_accepts_a_valid_application(): void
    {
        $response = $this->postJson('/api/v1/membership/applications', $this->validPayload());

        $response->assertCreated()
            ->assertJsonPath('data.tier', 'sovereign-partner')
            ->assertJsonPath('data.name', 'Amara Okafor')
            ->assertJsonPath('data.status', 'submitted')
            ->assertJsonStructure([
                'data' => [
                    'reference', 'tier', 'name', 'organisation', 'email',
                    'phone', 'country', 'statement', 'status', 'submitted_at',
                ],
            ]);

        $this->assertMatchesRegularExpression(
            '/^UGM-\d{4}-[A-Z0-9]{6}$/',
            $response->json('data.reference'),
        );
    }

    public function test_apply_endpoint_rejects_an_unknown_tier(): void
    {
        $response = $this->postJson('/api/v1/membership/applications', array_merge(
            $this->validPayload(),
            ['tier' => 'not-a-real-tier'],
        ));

        $response->assertStatus(422)->assertJsonValidationErrors('tier');
    }

    public function test_apply_endpoint_requires_the_core_fields(): void
    {
        $response = $this->postJson('/api/v1/membership/applications', []);

        $response->assertStatus(422)->assertJsonValidationErrors([
            'tier', 'applicant_name', 'email', 'statement',
        ]);
    }

    public function test_apply_endpoint_rejects_a_statement_that_is_too_short(): void
    {
        $response = $this->postJson('/api/v1/membership/applications', array_merge(
            $this->validPayload(),
            ['statement' => 'Too short.'],
        ));

        $response->assertStatus(422)->assertJsonValidationErrors('statement');
    }

    public function test_show_endpoint_returns_a_previously_submitted_application(): void
    {
        $submitted = $this->postJson('/api/v1/membership/applications', $this->validPayload());
        $reference = $submitted->json('data.reference');

        $response = $this->getJson("/api/v1/membership/applications/{$reference}");

        $response->assertOk()
            ->assertJsonPath('data.reference', $reference)
            ->assertJsonPath('data.email', 'amara.okafor@example.com');
    }

    public function test_show_endpoint_returns_404_for_an_unknown_reference(): void
    {
        $response = $this->getJson('/api/v1/membership/applications/UGM-2026-ZZZZZZ');

        $response->assertStatus(404);
    }

    /** @return array<string, mixed> */
    private function validPayload(): array
    {
        return [
            'tier' => 'sovereign-partner',
            'applicant_name' => 'Amara Okafor',
            'organisation' => 'Ministry of Trade',
            'email' => 'amara.okafor@example.com',
            'phone' => '+234 800 000 0000',
            'country' => 'Nigeria',
            'statement' => 'We are seeking a discreet strategic partner to advise on regional infrastructure financing.',
        ];
    }
}
