<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use Database\Seeders\MetricSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class MetricApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(MetricSeeder::class);
    }

    public function test_index_lists_all_metrics_in_position_order(): void
    {
        $response = $this->getJson('/api/v1/metrics');

        $response->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('data.0.slug', 'government-relationships')
            ->assertJsonPath('data.0.value', '250+')
            ->assertJsonPath('data.3.value', '$20B+')
            ->assertJsonPath('data.4.slug', 'discretion')
            ->assertJsonStructure([
                'data' => [
                    ['slug', 'value', 'label', 'label_lines', 'icon', 'position'],
                ],
            ]);
    }
}
