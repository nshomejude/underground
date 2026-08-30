<?php

declare(strict_types=1);

namespace Tests\Feature\Content;

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
}
