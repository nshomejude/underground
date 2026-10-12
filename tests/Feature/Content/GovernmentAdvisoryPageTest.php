<?php

declare(strict_types=1);

namespace Tests\Feature\Content;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * routes/lot1-gov-advisory.php is intentionally NOT required from
 * routes/web.php yet (see that file's own header comment — it is wired in
 * during integration). To exercise the real route + controller + view here,
 * register it directly against this test's application instance.
 */
final class GovernmentAdvisoryPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (! Route::has('government-advisory')) {
            require base_path('routes/lot1-gov-advisory.php');

            // Route::name() does not update the route collection's name
            // lookup table on its own (only the normal boot-time route-file
            // loading pass does that once, at the end). Since this file is
            // required after the app has already booted — on purpose, see
            // the class doc block above — the lookup table needs a manual
            // refresh so Route::has()/route() can see the new name.
            Route::getRoutes()->refreshNameLookups();
        }
    }

    public function test_the_page_renders_with_its_core_content(): void
    {
        $this->seed();

        $response = $this->get(route('government-advisory'));

        $response->assertOk();
        $response->assertSee('Government &amp; Head-of-State Advisory', false);
        $response->assertSee('Initial Diagnostic');
        $response->assertSee('Cooperation Roadmap');
        $response->assertSee('Follow-Up Reporting');
        $response->assertSee('Submarine Diplomacy');
        $response->assertSee('hypothetical');
        $response->assertSee(route('partners'), false);
    }

    public function test_the_request_cta_falls_back_to_the_confidential_inquiry_route(): void
    {
        $this->seed();

        $response = $this->get(route('government-advisory'));

        $response->assertOk();
        $response->assertSee(route('inquiries.create'), false);
    }

    public function test_it_is_linked_from_the_homepage_and_the_capabilities_index(): void
    {
        $this->seed();

        $this->get(route('home'))->assertOk()->assertSee(route('government-advisory'), false);
        $this->get(route('capabilities.index'))->assertOk()->assertSee(route('government-advisory'), false);
    }
}
