<?php

declare(strict_types=1);

namespace Tests\Feature\Content;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Models\CapabilityRecord;
use Tests\TestCase;

final class CapabilityPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_renders_an_existing_capability(): void
    {
        CapabilityRecord::factory()->create([
            'slug' => 'government-political-affairs',
            'title' => 'Government & Political Affairs',
            'icon' => 'landmark',
        ]);

        $response = $this->get(route('capabilities.show', 'government-political-affairs'));

        $response->assertOk();
        $response->assertSee('Government &amp;', false);
    }

    public function test_show_returns_404_for_an_unknown_slug(): void
    {
        $response = $this->get(route('capabilities.show', 'does-not-exist'));

        $response->assertNotFound();
    }

    public function test_show_returns_404_for_a_malformed_slug(): void
    {
        $response = $this->get('/capabilities/Not_A_Valid_Slug!');

        $response->assertNotFound();
    }
}
