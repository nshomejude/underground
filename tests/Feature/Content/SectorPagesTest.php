<?php

declare(strict_types=1);

namespace Tests\Feature\Content;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Models\SectorRecord;
use Tests\TestCase;

final class SectorPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_all_seeded_sectors(): void
    {
        SectorRecord::factory()->create([
            'slug' => 'energy-natural-resources',
            'name' => 'Energy & Natural Resources',
            'motif' => 'flame',
            'position' => 1,
        ]);
        SectorRecord::factory()->create([
            'slug' => 'finance-investments',
            'name' => 'Finance & Investments',
            'motif' => 'coins',
            'position' => 2,
        ]);

        $response = $this->get(route('sectors.index'));

        $response->assertOk();
        $response->assertSee('Energy &amp;', false);
        $response->assertSee('Finance &amp;', false);
    }

    public function test_show_renders_an_existing_sector(): void
    {
        SectorRecord::factory()->create([
            'slug' => 'government-public-sector',
            'name' => 'Government & Public Sector',
            'motif' => 'landmark',
        ]);

        $response = $this->get(route('sectors.show', 'government-public-sector'));

        $response->assertOk();
        $response->assertSee('Government &amp; Public Sector', false);
    }

    public function test_show_returns_404_for_an_unknown_slug(): void
    {
        $response = $this->get(route('sectors.show', 'does-not-exist'));

        $response->assertNotFound();
    }

    public function test_show_returns_404_for_a_malformed_slug(): void
    {
        $response = $this->get('/sectors/Not_A_Valid_Slug!');

        $response->assertNotFound();
    }
}
