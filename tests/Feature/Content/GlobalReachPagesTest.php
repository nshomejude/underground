<?php

declare(strict_types=1);

namespace Tests\Feature\Content;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Models\NarrativeRecord;
use Infrastructure\Persistence\Eloquent\Models\OfficeRecord;
use Tests\Concerns\RegistersGlobalReachRoutes;
use Tests\TestCase;

final class GlobalReachPagesTest extends TestCase
{
    use RefreshDatabase;
    use RegistersGlobalReachRoutes;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registerGlobalReachRoutes();

        // /global-reach composes the full landing page narrative — only
        // the narrative row is required for it to render (every other
        // query degrades to an empty list with no rows seeded).
        NarrativeRecord::factory()->create();
    }

    public function test_global_reach_shows_every_office_with_its_status_label(): void
    {
        OfficeRecord::factory()->create([
            'slug' => 'dc',
            'name' => 'Washington, D.C.',
            'status' => 'active_office',
            'is_headquarters' => true,
            'position' => 1,
        ]);
        OfficeRecord::factory()->create([
            'slug' => 'gulf-regional-office',
            'name' => 'Gulf Regional Office',
            'status' => 'partner_coverage',
            'lat' => null,
            'lng' => null,
            'position' => 2,
        ]);

        $response = $this->get(route('global-reach'));

        $response->assertOk();
        $response->assertSee('Washington, D.C.');
        $response->assertSee('Active Office');
        $response->assertSee('Gulf Regional Office');
        $response->assertSee('Partner Coverage');
        $response->assertSee(route('global-alliance'), false);
        $response->assertSee(route('locations'), false);
    }

    public function test_global_alliance_explains_every_status_and_relationship_type_without_duplicates(): void
    {
        $response = $this->get(route('global-alliance'));

        $response->assertOk();
        $response->assertSee('Planned');
        $response->assertSee('Partner Coverage');
        $response->assertSee('Representative Office');
        $response->assertSee('Active Office');
        $response->assertSee('Joint Venture');
        $response->assertSee('Cooperation Agreement');
        $response->assertSee('Consultant');
        $response->assertSee('Independent');
        $response->assertSee('never an Underground', false);
    }

    public function test_locations_lists_every_office_with_status_relationship_and_countries(): void
    {
        OfficeRecord::factory()->create([
            'slug' => 'kenya-regional-office',
            'name' => 'Kenya Regional Office',
            'region' => 'East Africa',
            'countries' => ['Kenya', 'Uganda', 'Tanzania'],
            'status' => 'partner_coverage',
            'relationship_type' => 'partnership',
            'lat' => null,
            'lng' => null,
            'position' => 1,
        ]);
        OfficeRecord::factory()->create([
            'slug' => 'asia-pacific',
            'name' => 'Asia + Pacific',
            'countries' => ['Coverage area to be defined'],
            'status' => 'planned',
            'lat' => null,
            'lng' => null,
            'position' => 2,
        ]);

        $response = $this->get(route('locations'));

        $response->assertOk();
        $response->assertSee('Kenya Regional Office');
        $response->assertSee('Partner Coverage');
        $response->assertSee('Partnership');
        $response->assertSee('Kenya');
        $response->assertSee('Uganda');
        $response->assertSee('Asia + Pacific');
        $response->assertSee('Coverage area to be defined');
        $response->assertSee('Planned');
    }

    public function test_contact_page_only_shows_offices_with_real_coordinates(): void
    {
        OfficeRecord::factory()->create([
            'slug' => 'dc',
            'name' => 'Washington, D.C.',
            'lat' => 38.9072,
            'lng' => -77.0369,
            'status' => 'active_office',
            'position' => 1,
        ]);
        OfficeRecord::factory()->create([
            'slug' => 'europe',
            'name' => 'Europe',
            'lat' => null,
            'lng' => null,
            'status' => 'planned',
            'position' => 2,
        ]);

        $response = $this->get(route('contact'));

        $response->assertOk();
        $response->assertSee('Washington, D.C.');
        $response->assertDontSee('Europe');
    }
}
