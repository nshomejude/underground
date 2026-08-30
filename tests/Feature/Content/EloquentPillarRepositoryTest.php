<?php

declare(strict_types=1);

namespace Tests\Feature\Content;

use Domain\Content\Entities\Pillar;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Models\PillarRecord;
use Infrastructure\Persistence\Eloquent\Repositories\EloquentPillarRepository;
use Tests\TestCase;

final class EloquentPillarRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_returns_pillars_ordered_by_position(): void
    {
        PillarRecord::factory()->create(['slug' => 'global-by-reach', 'title' => 'Global by Reach', 'position' => 4]);
        PillarRecord::factory()->create(['slug' => 'discreet-by-design', 'title' => 'Discreet by Design', 'position' => 1]);
        PillarRecord::factory()->create(['slug' => 'effective-by-execution', 'title' => 'Effective by Execution', 'position' => 3]);
        PillarRecord::factory()->create(['slug' => 'strategic-by-nature', 'title' => 'Strategic by Nature', 'position' => 2]);

        $repository = new EloquentPillarRepository;

        $pillars = $repository->all();

        $this->assertSame(
            ['discreet-by-design', 'strategic-by-nature', 'effective-by-execution', 'global-by-reach'],
            array_map(fn ($pillar) => $pillar->slug->value, $pillars),
        );
        $this->assertSame('Discreet by Design', $pillars[0]->title);
    }

    public function test_find_by_slug_returns_the_matching_pillar(): void
    {
        PillarRecord::factory()->create(['slug' => 'discreet-by-design', 'title' => 'Discreet by Design']);

        $repository = new EloquentPillarRepository;

        $pillar = $repository->findBySlug(Slug::fromString('discreet-by-design'));

        $this->assertNotNull($pillar);
        $this->assertSame('Discreet by Design', $pillar->title);
    }

    public function test_find_by_slug_returns_null_for_a_missing_slug(): void
    {
        $repository = new EloquentPillarRepository;

        $this->assertNull($repository->findBySlug(Slug::fromString('does-not-exist')));
    }

    public function test_save_creates_a_new_pillar(): void
    {
        $repository = new EloquentPillarRepository;

        $repository->save(new Pillar(
            slug: Slug::fromString('global-by-reach'),
            title: 'Global by Reach',
            qualifier: 'by Reach',
            icon: 'globe',
            position: 4,
        ));

        $pillar = $repository->findBySlug(Slug::fromString('global-by-reach'));

        $this->assertNotNull($pillar);
        $this->assertSame('Global by Reach', $pillar->title);
    }

    public function test_delete_removes_the_pillar(): void
    {
        PillarRecord::factory()->create(['slug' => 'discreet-by-design']);

        $repository = new EloquentPillarRepository;
        $repository->delete(Slug::fromString('discreet-by-design'));

        $this->assertNull($repository->findBySlug(Slug::fromString('discreet-by-design')));
    }
}
