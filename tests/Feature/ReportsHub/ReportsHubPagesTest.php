<?php

declare(strict_types=1);

namespace Tests\Feature\ReportsHub;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Models\InsightRecord;
use Tests\TestCase;

final class ReportsHubPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_published_reports(): void
    {
        InsightRecord::factory()->create([
            'slug' => 'discretion-as-strategy',
            'title' => 'Discretion as Strategy',
        ]);
        InsightRecord::factory()->unpublished()->create([
            'slug' => 'unpublished-piece',
            'title' => 'Should Not Appear',
        ]);

        $response = $this->get(route('reports-hub.index'));

        $response->assertOk();
        $response->assertSee('Discretion as Strategy');
        $response->assertDontSee('Should Not Appear');
    }

    public function test_show_renders_a_published_report(): void
    {
        InsightRecord::factory()->create([
            'slug' => 'infrastructure-as-influence',
            'title' => 'Infrastructure as Influence',
        ]);

        $response = $this->get(route('reports-hub.show', 'infrastructure-as-influence'));

        $response->assertOk();
        $response->assertSee('Infrastructure as Influence');
    }

    public function test_show_returns_404_for_an_unknown_slug(): void
    {
        $response = $this->get(route('reports-hub.show', 'does-not-exist'));

        $response->assertNotFound();
    }

    public function test_show_returns_404_for_a_malformed_slug(): void
    {
        $response = $this->get('/reports-hub/Not_A_Valid_Slug!');

        $response->assertNotFound();
    }

    public function test_old_insights_index_redirects_to_reports_hub(): void
    {
        $response = $this->get('/insights');

        $response->assertRedirect(route('reports-hub.index'));
    }

    public function test_old_insights_show_redirects_preserving_slug(): void
    {
        InsightRecord::factory()->create(['slug' => 'discretion-as-strategy']);

        $response = $this->get('/insights/discretion-as-strategy');

        $response->assertRedirect(route('reports-hub.show', 'discretion-as-strategy'));
    }
}
