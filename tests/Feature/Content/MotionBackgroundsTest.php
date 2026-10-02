<?php

declare(strict_types=1);

namespace Tests\Feature\Content;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Models\SiteSettingRecord;
use Tests\TestCase;

final class MotionBackgroundsTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_hero_renders_the_gear_background_by_default(): void
    {
        $this->seed();

        $this->get('/')->assertOk()->assertSee('gear-bg', false)->assertSee('gear-spin', false);
    }

    public function test_gear_background_can_be_switched_off_from_site_settings(): void
    {
        $this->seed();
        SiteSettingRecord::query()->update(['gear_animation_enabled' => false]);

        $this->get('/')->assertOk()->assertDontSee('gear-bg', false);
    }

    public function test_network_grid_renders_on_global_reach_and_partners_and_can_be_switched_off(): void
    {
        $this->seed();

        $this->get('/global-reach')->assertOk()->assertSee('net-bg', false);
        $this->get('/partners')->assertOk()->assertSee('net-bg', false);

        SiteSettingRecord::query()->update(['network_animation_enabled' => false]);

        $this->get('/global-reach')->assertOk()->assertDontSee('net-bg', false);
        $this->get('/partners')->assertOk()->assertDontSee('net-bg', false);
    }
}
