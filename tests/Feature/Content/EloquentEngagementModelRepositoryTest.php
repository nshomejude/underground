<?php

declare(strict_types=1);

namespace Tests\Feature\Content;

use Domain\Shared\ValueObjects\Slug;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Models\EngagementModelRecord;
use Infrastructure\Persistence\Eloquent\Repositories\EloquentEngagementModelRepository;
use Tests\TestCase;

final class EloquentEngagementModelRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_returns_engagement_models_ordered_by_position(): void
    {
        EngagementModelRecord::factory()->create(['slug' => 'crisis-management-special-situations', 'position' => 4]);
        EngagementModelRecord::factory()->create(['slug' => 'strategic-advisory-retainers', 'position' => 1]);
        EngagementModelRecord::factory()->create(['slug' => 'government-affairs-lobbying', 'position' => 3]);
        EngagementModelRecord::factory()->create(['slug' => 'project-based-engagements', 'position' => 2]);

        $repository = new EloquentEngagementModelRepository;

        $this->assertSame(
            [
                'strategic-advisory-retainers',
                'project-based-engagements',
                'government-affairs-lobbying',
                'crisis-management-special-situations',
            ],
            array_map(fn ($model) => $model->slug->value, $repository->all()),
        );
    }

    public function test_find_by_slug_returns_null_for_a_missing_slug(): void
    {
        $repository = new EloquentEngagementModelRepository;

        $this->assertNull($repository->findBySlug(Slug::fromString('does-not-exist')));
    }

    public function test_find_by_slug_returns_the_matching_engagement_model(): void
    {
        EngagementModelRecord::factory()->create([
            'slug' => 'strategic-advisory-retainers',
            'name' => 'Strategic Advisory Retainers',
        ]);

        $repository = new EloquentEngagementModelRepository;

        $model = $repository->findBySlug(Slug::fromString('strategic-advisory-retainers'));

        $this->assertNotNull($model);
        $this->assertSame('Strategic Advisory Retainers', $model->name);
    }
}
