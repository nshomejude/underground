<?php

declare(strict_types=1);

namespace Tests\Feature\Content;

use Domain\Content\Entities\Office;
use Domain\Content\ValueObjects\OfficeStatus;
use Domain\Content\ValueObjects\RelationshipType;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Models\OfficeRecord;
use Infrastructure\Persistence\Eloquent\Repositories\EloquentOfficeRepository;
use Tests\TestCase;

final class EloquentOfficeRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_save_creates_a_new_office(): void
    {
        $repository = new EloquentOfficeRepository;

        $repository->save(new Office(
            slug: Slug::fromString('freshly-created'),
            name: 'Freshly Created',
            region: 'Nowhere',
            address: null,
            email: null,
            phone: null,
            blurb: 'A blurb.',
            lat: null,
            lng: null,
            countries: ['Nowhere'],
            status: OfficeStatus::Planned,
            relationshipType: RelationshipType::Partnership,
            isHeadquarters: false,
            position: 9,
        ));

        $this->assertDatabaseHas('offices', [
            'slug' => 'freshly-created',
            'name' => 'Freshly Created',
            'status' => 'planned',
            'relationship_type' => 'partnership',
        ]);
    }

    public function test_save_updates_an_existing_office_by_slug(): void
    {
        OfficeRecord::factory()->create(['slug' => 'to-update', 'name' => 'Before', 'status' => 'planned']);

        $repository = new EloquentOfficeRepository;

        $repository->save(new Office(
            slug: Slug::fromString('to-update'),
            name: 'After',
            region: 'Somewhere',
            address: null,
            email: null,
            phone: null,
            blurb: 'A blurb.',
            lat: null,
            lng: null,
            countries: [],
            status: OfficeStatus::ActiveOffice,
            relationshipType: RelationshipType::Independent,
            isHeadquarters: true,
            position: 1,
        ));

        $this->assertDatabaseCount('offices', 1);
        $this->assertDatabaseHas('offices', [
            'slug' => 'to-update',
            'name' => 'After',
            'status' => 'active_office',
            'is_headquarters' => true,
        ]);
    }

    public function test_delete_removes_the_office_with_the_given_slug(): void
    {
        OfficeRecord::factory()->create(['slug' => 'to-delete']);
        OfficeRecord::factory()->create(['slug' => 'to-keep']);

        $repository = new EloquentOfficeRepository;
        $repository->delete(Slug::fromString('to-delete'));

        $this->assertDatabaseMissing('offices', ['slug' => 'to-delete']);
        $this->assertDatabaseHas('offices', ['slug' => 'to-keep']);
    }

    public function test_all_returns_offices_ordered_by_position(): void
    {
        OfficeRecord::factory()->create(['slug' => 'third', 'position' => 3]);
        OfficeRecord::factory()->create(['slug' => 'first', 'position' => 1]);
        OfficeRecord::factory()->create(['slug' => 'second', 'position' => 2]);

        $repository = new EloquentOfficeRepository;

        $this->assertSame(
            ['first', 'second', 'third'],
            array_map(fn ($office) => $office->slug->value, $repository->all()),
        );
    }

    public function test_find_by_slug_returns_the_matching_office_with_its_countries_and_enums(): void
    {
        OfficeRecord::factory()->create([
            'slug' => 'gulf-regional-office',
            'name' => 'Gulf Regional Office',
            'countries' => ['Bahrain', 'Kuwait', 'Oman', 'Qatar', 'United Arab Emirates'],
            'status' => 'partner_coverage',
            'relationship_type' => 'partnership',
            'lat' => null,
            'lng' => null,
        ]);

        $repository = new EloquentOfficeRepository;

        $office = $repository->findBySlug(Slug::fromString('gulf-regional-office'));

        $this->assertNotNull($office);
        $this->assertSame('Gulf Regional Office', $office->name);
        $this->assertSame(['Bahrain', 'Kuwait', 'Oman', 'Qatar', 'United Arab Emirates'], $office->countries);
        $this->assertSame(OfficeStatus::PartnerCoverage, $office->status);
        $this->assertSame(RelationshipType::Partnership, $office->relationshipType);
        $this->assertFalse($office->hasCoordinates());
    }

    public function test_find_by_slug_returns_null_for_a_missing_slug(): void
    {
        $repository = new EloquentOfficeRepository;

        $this->assertNull($repository->findBySlug(Slug::fromString('does-not-exist')));
    }
}
