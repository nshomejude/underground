<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Models\InsightRecord;
use Tests\TestCase;

final class InsightApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_published_insights_most_recent_first(): void
    {
        InsightRecord::factory()->create([
            'slug' => 'oldest-piece',
            'title' => 'Oldest Piece',
            'published_at' => now()->subDays(10),
        ]);
        InsightRecord::factory()->create([
            'slug' => 'newest-piece',
            'title' => 'Newest Piece',
            'published_at' => now()->subDay(),
        ]);
        InsightRecord::factory()->unpublished()->create([
            'slug' => 'unpublished-piece',
            'title' => 'Unpublished Piece',
        ]);

        $response = $this->getJson('/api/v1/insights');

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.slug', 'newest-piece')
            ->assertJsonPath('data.1.slug', 'oldest-piece')
            ->assertJsonStructure([
                'data' => [
                    ['slug', 'title', 'category', 'excerpt', 'body', 'reading_minutes', 'published_at'],
                ],
            ]);
    }

    public function test_index_respects_the_limit_query_parameter(): void
    {
        InsightRecord::factory()->create(['slug' => 'first', 'published_at' => now()->subDays(2)]);
        InsightRecord::factory()->create(['slug' => 'second', 'published_at' => now()->subDay()]);

        $response = $this->getJson('/api/v1/insights?limit=1');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'second');
    }

    public function test_show_returns_a_published_insight_by_slug(): void
    {
        InsightRecord::factory()->create([
            'slug' => 'infrastructure-as-influence',
            'title' => 'Infrastructure as Influence',
        ]);

        $response = $this->getJson('/api/v1/insights/infrastructure-as-influence');

        $response->assertOk()
            ->assertJsonPath('data.slug', 'infrastructure-as-influence')
            ->assertJsonPath('data.title', 'Infrastructure as Influence')
            ->assertJsonStructure([
                'data' => ['slug', 'title', 'category', 'excerpt', 'body', 'reading_minutes', 'published_at'],
            ]);
    }

    public function test_show_returns_404_for_an_unknown_slug(): void
    {
        $response = $this->getJson('/api/v1/insights/does-not-exist');

        $response->assertStatus(404)->assertJson(['message' => 'Not found']);
    }

    public function test_show_returns_404_for_an_unpublished_insight(): void
    {
        InsightRecord::factory()->unpublished()->create(['slug' => 'not-yet-live']);

        $response = $this->getJson('/api/v1/insights/not-yet-live');

        $response->assertStatus(404)->assertJson(['message' => 'Not found']);
    }

    public function test_show_returns_404_for_a_malformed_slug(): void
    {
        $response = $this->getJson('/api/v1/insights/Not_A_Valid_Slug!');

        $response->assertStatus(404)->assertJson(['message' => 'Not found']);
    }
}
