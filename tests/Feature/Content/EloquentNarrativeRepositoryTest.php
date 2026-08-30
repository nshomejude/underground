<?php

declare(strict_types=1);

namespace Tests\Feature\Content;

use Domain\Shared\Exceptions\DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Models\NarrativeRecord;
use Infrastructure\Persistence\Eloquent\Repositories\EloquentNarrativeRepository;
use Tests\TestCase;

final class EloquentNarrativeRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_current_throws_when_no_narrative_has_been_authored(): void
    {
        $repository = new EloquentNarrativeRepository;

        $this->expectException(DomainException::class);

        $repository->current();
    }

    public function test_current_maps_the_latest_narrative_row_to_the_entity(): void
    {
        NarrativeRecord::factory()->create([
            'company' => 'Underground',
            'tagline' => 'Strategic Influence. Real Outcomes.',
            'headline' => ['Power Beneath', 'the Surface.'],
            'navigation' => [
                ['label' => 'Capabilities', 'href' => '#capabilities'],
            ],
        ]);

        $repository = new EloquentNarrativeRepository;

        $narrative = $repository->current();

        $this->assertSame('Underground', $narrative->company);
        $this->assertSame('Strategic Influence. Real Outcomes.', $narrative->tagline);
        $this->assertSame(['Power Beneath', 'the Surface.'], $narrative->headline);
        $this->assertSame([['label' => 'Capabilities', 'href' => '#capabilities']], $narrative->navigation);
    }

    public function test_current_returns_the_most_recently_created_row(): void
    {
        NarrativeRecord::factory()->create(['tagline' => 'Old Tagline']);
        NarrativeRecord::factory()->create(['tagline' => 'Newest Tagline']);

        $repository = new EloquentNarrativeRepository;

        $this->assertSame('Newest Tagline', $repository->current()->tagline);
    }
}
