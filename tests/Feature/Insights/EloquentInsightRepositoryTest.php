<?php

declare(strict_types=1);

namespace Tests\Feature\Insights;

use DateTimeImmutable;
use Domain\Insights\Entities\Insight;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Models\InsightRecord;
use Infrastructure\Persistence\Eloquent\Repositories\EloquentInsightRepository;
use Tests\TestCase;

final class EloquentInsightRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_returns_every_insight_published_or_not_most_recent_first(): void
    {
        InsightRecord::factory()->create(['slug' => 'first-created', 'created_at' => now()->subDays(2)]);
        InsightRecord::factory()->unpublished()->create(['slug' => 'second-created', 'created_at' => now()->subDay()]);
        InsightRecord::factory()->create(['slug' => 'third-created', 'created_at' => now()]);

        $repository = new EloquentInsightRepository;

        $this->assertSame(
            ['third-created', 'second-created', 'first-created'],
            array_map(fn ($insight) => $insight->slug->value, $repository->all()),
        );
    }

    public function test_save_creates_a_new_insight(): void
    {
        $repository = new EloquentInsightRepository;

        $repository->save(new Insight(
            slug: Slug::fromString('freshly-created'),
            title: 'Freshly Created',
            category: 'Strategy',
            excerpt: 'An excerpt.',
            body: 'A body.',
            publishedAt: null,
        ));

        $this->assertDatabaseHas('insights', ['slug' => 'freshly-created', 'title' => 'Freshly Created']);
    }

    public function test_save_updates_an_existing_insight_by_slug(): void
    {
        InsightRecord::factory()->create(['slug' => 'to-update', 'title' => 'Before']);

        $repository = new EloquentInsightRepository;

        $repository->save(new Insight(
            slug: Slug::fromString('to-update'),
            title: 'After',
            category: 'Strategy',
            excerpt: 'An excerpt.',
            body: 'A body.',
            publishedAt: new DateTimeImmutable('2026-01-01'),
        ));

        $this->assertDatabaseCount('insights', 1);
        $this->assertDatabaseHas('insights', ['slug' => 'to-update', 'title' => 'After']);
    }

    public function test_delete_removes_the_insight_with_the_given_slug(): void
    {
        InsightRecord::factory()->create(['slug' => 'to-delete']);
        InsightRecord::factory()->create(['slug' => 'to-keep']);

        $repository = new EloquentInsightRepository;
        $repository->delete(Slug::fromString('to-delete'));

        $this->assertDatabaseMissing('insights', ['slug' => 'to-delete']);
        $this->assertDatabaseHas('insights', ['slug' => 'to-keep']);
    }

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
