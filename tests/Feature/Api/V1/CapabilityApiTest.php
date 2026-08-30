<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use Database\Seeders\CapabilitySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CapabilityApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(CapabilitySeeder::class);
    }

    public function test_index_lists_all_capabilities_in_position_order(): void
    {
        $response = $this->getJson('/api/v1/capabilities');

        $response->assertOk()
            ->assertJsonCount(8, 'data')
            ->assertJsonPath('data.0.slug', 'government-political-affairs')
            ->assertJsonPath('data.1.slug', 'international-relations-diplomacy')
            ->assertJsonPath('data.2.slug', 'strategic-intelligence-analysis')
            ->assertJsonStructure([
                'data' => [
                    ['slug', 'title', 'title_lines', 'summary', 'icon', 'position', 'featured'],
                ],
            ]);
    }

    public function test_show_returns_a_capability_by_slug(): void
    {
        $response = $this->getJson('/api/v1/capabilities/government-political-affairs');

        $response->assertOk()
            ->assertJsonPath('data.slug', 'government-political-affairs')
            ->assertJsonPath('data.title', 'Government & Political Affairs')
            ->assertJsonPath('data.featured', true)
            ->assertJsonStructure([
                'data' => ['slug', 'title', 'title_lines', 'summary', 'icon', 'position', 'featured'],
            ]);
    }

    public function test_show_returns_404_for_an_unknown_slug(): void
    {
        $response = $this->getJson('/api/v1/capabilities/not-a-real-capability');

        $response->assertStatus(404)->assertJson(['message' => 'Not found']);
    }

    public function test_show_returns_404_for_a_malformed_slug(): void
    {
        $response = $this->getJson('/api/v1/capabilities/'.rawurlencode('not a slug!'));

        $response->assertStatus(404)->assertJson(['message' => 'Not found']);
    }
}
