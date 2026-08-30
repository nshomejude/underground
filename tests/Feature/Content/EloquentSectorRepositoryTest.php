<?php

declare(strict_types=1);

namespace Tests\Feature\Content;

use Domain\Content\Entities\Sector;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Models\SectorRecord;
use Infrastructure\Persistence\Eloquent\Repositories\EloquentSectorRepository;
use Tests\TestCase;

final class EloquentSectorRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_returns_sectors_ordered_by_position(): void
    {
        SectorRecord::factory()->create(['slug' => 'telecommunications', 'position' => 3]);
        SectorRecord::factory()->create(['slug' => 'oil-gas', 'position' => 1]);
        SectorRecord::factory()->create(['slug' => 'mining-natural-resources', 'position' => 2]);

        $repository = new EloquentSectorRepository;

        $this->assertSame(
            ['oil-gas', 'mining-natural-resources', 'telecommunications'],
            array_map(fn ($sector) => $sector->slug->value, $repository->all()),
        );
    }

    public function test_find_by_slug_returns_the_matching_sector(): void
    {
        SectorRecord::factory()->create([
            'slug' => 'oil-gas',
            'name' => 'Oil & Gas',
            'summary' => 'Energy majors and national oil companies.',
            'motif' => 'skyline',
            'position' => 1,
        ]);

        $repository = new EloquentSectorRepository;

        $sector = $repository->findBySlug(Slug::fromString('oil-gas'));

        $this->assertNotNull($sector);
        $this->assertSame('Oil & Gas', $sector->name);
        $this->assertSame(['Oil &', 'Gas'], $sector->nameLines());
        $this->assertSame('skyline', $sector->motif);
    }

    public function test_find_by_slug_returns_null_for_a_missing_slug(): void
    {
        $repository = new EloquentSectorRepository;

        $this->assertNull($repository->findBySlug(Slug::fromString('does-not-exist')));
    }

    public function test_save_creates_a_new_sector(): void
    {
        $repository = new EloquentSectorRepository;

        $repository->save(new Sector(
            slug: Slug::fromString('aerospace-defence'),
            name: 'Aerospace & Defence',
            summary: 'Prime contractors and defence ministries.',
            motif: 'radar',
            position: 5,
        ));

        $sector = $repository->findBySlug(Slug::fromString('aerospace-defence'));

        $this->assertNotNull($sector);
        $this->assertSame('Aerospace & Defence', $sector->name);
        $this->assertSame(5, $sector->position);
    }

    public function test_save_updates_an_existing_sector_in_place(): void
    {
        SectorRecord::factory()->create(['slug' => 'oil-gas', 'name' => 'Oil & Gas', 'position' => 1]);

        $repository = new EloquentSectorRepository;

        $repository->save(new Sector(
            slug: Slug::fromString('oil-gas'),
            name: 'Oil, Gas & Energy',
            summary: 'Updated summary.',
            motif: 'skyline',
            position: 2,
        ));

        $this->assertCount(1, SectorRecord::all());
        $sector = $repository->findBySlug(Slug::fromString('oil-gas'));
        $this->assertSame('Oil, Gas & Energy', $sector->name);
        $this->assertSame(2, $sector->position);
    }

    public function test_save_with_an_original_slug_renames_the_record(): void
    {
        SectorRecord::factory()->create(['slug' => 'old-slug', 'name' => 'Old Name', 'position' => 1]);

        $repository = new EloquentSectorRepository;

        $repository->save(
            new Sector(
                slug: Slug::fromString('new-slug'),
                name: 'New Name',
                summary: 'Renamed summary.',
                motif: 'grid',
                position: 1,
            ),
            originalSlug: Slug::fromString('old-slug'),
        );

        $this->assertCount(1, SectorRecord::all());
        $this->assertNull($repository->findBySlug(Slug::fromString('old-slug')));
        $this->assertNotNull($repository->findBySlug(Slug::fromString('new-slug')));
    }

    public function test_delete_removes_the_sector(): void
    {
        SectorRecord::factory()->create(['slug' => 'oil-gas']);

        $repository = new EloquentSectorRepository;
        $repository->delete(Slug::fromString('oil-gas'));

        $this->assertNull($repository->findBySlug(Slug::fromString('oil-gas')));
    }
}
