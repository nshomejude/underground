<?php

declare(strict_types=1);

namespace Tests\Feature\Content;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_renders_the_hero_and_seeded_capabilities(): void
    {
        $this->seed();

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Power Beneath');
        $response->assertSee('the Surface.');
        $response->assertSee('Underground Network — Power Beneath the Surface');
        $response->assertSee('Government Affairs &amp; Institutional Relations', false);
        $response->assertSee(route('inquiries.create'), false);
    }

    public function test_home_renders_every_seeded_metric_and_engagement_model(): void
    {
        $this->seed();

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('250+');
        $response->assertSee('Strategic Advisory Retainers');
    }
}
