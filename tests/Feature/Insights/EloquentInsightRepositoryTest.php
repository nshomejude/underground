<?php

declare(strict_types=1);

namespace Tests\Feature\Insights;

use Domain\Shared\ValueObjects\Slug;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Models\InsightRecord;
use Infrastructure\Persistence\Eloquent\Repositories\EloquentInsightRepository;
use Tests\TestCase;

final class EloquentInsightRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_orders_most_recent_first_and_respects_limit(): void
    {
        InsightRecord::factory()->create([
            'slug' => 'oldest-piece',
            'published_at' => now()->subMonths(3),
        ]);
        InsightRecord::factory()->create([
            'slug' => 'newest-piece',
            'published_at' => now()->subDay(),
        ]);
        InsightRecord::factory()->create([
            'slug' => 'middle-piece',
            'published_at' => now()->subMonth(),
        ]);
        InsightRecord::factory()->unpublished()->create([
            'slug' => 'draft-piece',
        ]);

        $repository = new EloquentInsightRepository;

        $all = $repository->published();
        $this->assertSame(
            ['newest-piece', 'middle-piece', 'oldest-piece'],
            array_map(fn ($insight) => $insight->slug->value, $all),
        );

        $limited = $repository->published(limit: 2);
        $this->assertCount(2, $limited);
        $this->assertSame(
            ['newest-piece', 'middle-piece'],
            array_map(fn ($insight) => $insight->slug->value, $limited),
        );
    }

    public function test_find_by_slug_returns_null_for_a_missing_slug(): void
    {
        $repository = new EloquentInsightRepository;

        $this->assertNull($repository->findBySlug(Slug::fromString('does-not-exist')));
    }

    public function test_find_by_slug_returns_the_matching_insight(): void
    {
        InsightRecord::factory()->create([
            'slug' => 'discretion-as-strategy',
            'title' => 'Discretion as Strategy',
        ]);

        $repository = new EloquentInsightRepository;

        $insight = $repository->findBySlug(Slug::fromString('discretion-as-strategy'));

        $this->assertNotNull($insight);
        $this->assertSame('Discretion as Strategy', $insight->title);
    }
}
