<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Confirms the branded views in resources/views/errors are the ones Laravel
 * actually renders for each status, rather than falling back to the
 * framework's default error content or the debug/Whoops page. Debug mode is
 * forced off for every case here since APP_DEBUG=true (the local/testing
 * default) would otherwise render the debug page instead of these views —
 * that's expected framework behaviour, not something these tests work around
 * beyond disabling it for the duration of the assertion.
 */
final class ErrorPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.debug' => false]);
    }

    public function test_an_unknown_url_shows_the_branded_404_page(): void
    {
        $this->get('/this-page-does-not-exist-anywhere')
            ->assertNotFound()
            ->assertSee('This Page Has Moved in the Shadows')
            ->assertDontSee('Symfony\\', false);
    }

    public function test_a_logged_in_non_admin_sees_the_branded_403_page(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)->get(route('admin.applications.index'))
            ->assertForbidden()
            ->assertSee("This Area Isn't Open to You", false)
            ->assertDontSee('admin.applications');
    }

    public function test_a_token_mismatch_shows_the_branded_419_page(): void
    {
        Route::get('/__test/419', fn () => abort(419));

        $this->get('/__test/419')
            ->assertStatus(419)
            ->assertSee('Your Session Expired');
    }

    public function test_throttling_the_inquiry_endpoint_shows_the_branded_429_page(): void
    {
        $payload = [
            'name' => 'Jordan Achebe',
            'organisation' => 'Achebe Holdings',
            'email' => 'jordan@achebe-holdings.example',
            'phone' => '+1 202 555 0100',
            'country' => 'Nigeria',
            'interest' => 'investment-capital-strategy',
            'brief' => 'We are exploring a strategic capital raise across two jurisdictions.',
        ];

        for ($i = 0; $i < 10; $i++) {
            $this->post(route('inquiries.store'), $payload);
        }

        $this->post(route('inquiries.store'), $payload)
            ->assertStatus(429)
            ->assertSee('Slow Down a Moment');
    }

    public function test_an_unhandled_exception_shows_the_branded_500_page(): void
    {
        Route::get('/__test/500', fn () => throw new \RuntimeException('boom'));

        $this->get('/__test/500')
            ->assertStatus(500)
            ->assertSee('Something Went Wrong on Our End')
            ->assertDontSee('boom');
    }

    public function test_service_unavailable_shows_the_branded_503_page(): void
    {
        Route::get('/__test/503', fn () => abort(503));

        $this->get('/__test/503')
            ->assertStatus(503)
            ->assertSee('Briefly Unavailable');
    }
}
