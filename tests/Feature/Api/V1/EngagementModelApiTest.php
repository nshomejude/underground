<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use Database\Seeders\EngagementModelSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class EngagementModelApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(EngagementModelSeeder::class);
    }

    public function test_index_lists_all_engagement_models_in_position_order(): void
    {
        $response = $this->getJson('/api/v1/engagement-models');

        $response->assertOk()
            ->assertJsonCount(4, 'data')
            ->assertJsonPath('data.0.slug', 'strategic-advisory-retainers')
            ->assertJsonPath('data.3.slug', 'crisis-management-special-situations')
            ->assertJsonStructure([
                'data' => [
                    ['slug', 'name', 'summary', 'icon', 'position'],
                ],
            ]);
    }
}
