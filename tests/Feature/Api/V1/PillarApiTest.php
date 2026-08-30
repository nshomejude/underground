<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use Database\Seeders\PillarSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PillarApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PillarSeeder::class);
    }

    public function test_index_lists_all_pillars_in_position_order(): void
    {
        $response = $this->getJson('/api/v1/pillars');

        $response->assertOk()
            ->assertJsonCount(4, 'data')
            ->assertJsonPath('data.0.slug', 'discreet-by-design')
            ->assertJsonPath('data.3.slug', 'global-by-reach')
            ->assertJsonStructure([
                'data' => [
                    ['slug', 'title', 'qualifier', 'icon', 'position'],
                ],
            ]);
    }
}
