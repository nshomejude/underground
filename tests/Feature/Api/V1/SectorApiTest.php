<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use Database\Seeders\SectorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class SectorApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SectorSeeder::class);
    }

    public function test_index_lists_all_sectors_in_position_order(): void
    {
        $response = $this->getJson('/api/v1/sectors');

        $response->assertOk()
            ->assertJsonCount(6, 'data')
            ->assertJsonPath('data.0.slug', 'government-public-sector')
            ->assertJsonPath('data.5.slug', 'finance-investments')
            ->assertJsonStructure([
                'data' => [
                    ['slug', 'name', 'name_lines', 'summary', 'motif', 'position'],
                ],
            ]);
    }

    public function test_show_returns_a_sector_by_slug(): void
    {
        $response = $this->getJson('/api/v1/sectors/defense-security');

        $response->assertOk()
            ->assertJsonPath('data.slug', 'defense-security')
            ->assertJsonPath('data.name', 'Defense & Security')
            ->assertJsonStructure([
                'data' => ['slug', 'name', 'name_lines', 'summary', 'motif', 'position'],
            ]);
    }

    public function test_show_returns_404_for_an_unknown_slug(): void
    {
        $response = $this->getJson('/api/v1/sectors/not-a-real-sector');

        $response->assertStatus(404)->assertJson(['message' => 'Not found']);
    }
}
