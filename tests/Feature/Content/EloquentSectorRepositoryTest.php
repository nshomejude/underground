<?php

declare(strict_types=1);

namespace Tests\Feature\Content;

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
}
