<?php

declare(strict_types=1);

namespace Tests\Feature\Content;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Models\EngagementModelRecord;
use Tests\TestCase;

final class EngagementModelPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_all_engagement_models(): void
    {
        EngagementModelRecord::factory()->create([
            'slug' => 'retained-advisory',
            'name' => 'Retained Advisory',
            'position' => 1,
        ]);
        EngagementModelRecord::factory()->create([
            'slug' => 'discrete-mandate',
            'name' => 'Discrete Mandate',
            'position' => 2,
        ]);

        $response = $this->get(route('engagement-models.index'));

        $response->assertOk();
        $response->assertSee('Retained Advisory');
        $response->assertSee('Discrete Mandate');
    }

    public function test_show_renders_an_existing_engagement_model(): void
    {
        EngagementModelRecord::factory()->create([
            'slug' => 'retained-advisory',
            'name' => 'Retained Advisory',
            'icon' => 'handshake',
        ]);

        $response = $this->get(route('engagement-models.show', 'retained-advisory'));

        $response->assertOk();
        $response->assertSee('Retained Advisory');
    }

    public function test_show_returns_404_for_an_unknown_slug(): void
    {
        $response = $this->get(route('engagement-models.show', 'does-not-exist'));

        $response->assertNotFound();
    }

    public function test_show_returns_404_for_a_malformed_slug(): void
    {
        $response = $this->get('/engagement-models/Not_A_Valid_Slug!');

        $response->assertNotFound();
    }
}
