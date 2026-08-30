<?php

declare(strict_types=1);

namespace Tests\Feature\Navigation;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class NavLinksTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return list<string>
     */
    private static function deadNavLabels(): array
    {
        return ['Capabilities', 'Expertise', 'Global Reach', 'Insights', 'Careers'];
    }

    public function test_homepage_has_no_dead_hash_links_for_primary_nav_items(): void
    {
        $this->seed();

        $response = $this->get('/');

        $response->assertOk();
        $response->assertDontSee('href="#"', false);
    }

    public function test_homepage_wires_every_previously_dead_nav_label_to_a_real_destination(): void
    {
        $this->seed();

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee(url('/').'#capabilities', false);
        $response->assertSee(url('/').'#sectors', false);
        $response->assertSee(url('/').'#reach', false);
        $response->assertSee(route('insights.index'), false);
        $response->assertSee(route('careers'), false);
    }
}
