<?php

declare(strict_types=1);

namespace Tests\Feature\Insights;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Models\InsightRecord;
use Tests\TestCase;

final class InsightPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_published_insights(): void
    {
        InsightRecord::factory()->create([
            'slug' => 'discretion-as-strategy',
            'title' => 'Discretion as Strategy',
        ]);
        InsightRecord::factory()->unpublished()->create([
            'slug' => 'unpublished-piece',
            'title' => 'Should Not Appear',
        ]);

        $response = $this->get(route('insights.index'));

        $response->assertOk();
        $response->assertSee('Discretion as Strategy');
        $response->assertDontSee('Should Not Appear');
    }

    public function test_show_renders_a_published_insight(): void
    {
        InsightRecord::factory()->create([
            'slug' => 'infrastructure-as-influence',
            'title' => 'Infrastructure as Influence',
        ]);

        $response = $this->get(route('insights.show', 'infrastructure-as-influence'));

        $response->assertOk();
        $response->assertSee('Infrastructure as Influence');
    }

    public function test_show_returns_404_for_an_unknown_slug(): void
    {
        $response = $this->get(route('insights.show', 'does-not-exist'));

        $response->assertNotFound();
    }

    public function test_show_returns_404_for_a_malformed_slug(): void
    {
        $response = $this->get('/insights/Not_A_Valid_Slug!');

        $response->assertNotFound();
    }
}
