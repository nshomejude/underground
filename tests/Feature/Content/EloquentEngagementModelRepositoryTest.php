<?php

declare(strict_types=1);

namespace Tests\Feature\Content;

use Domain\Content\Entities\EngagementModel;
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

    public function test_save_creates_a_new_engagement_model(): void
    {
        $repository = new EloquentEngagementModelRepository;

        $repository->save(new EngagementModel(
            slug: Slug::fromString('crisis-management-special-situations'),
            name: 'Crisis Management & Special Situations',
            summary: 'Rapid-response engagements.',
            icon: 'radar',
            position: 4,
        ));

        $model = $repository->findBySlug(Slug::fromString('crisis-management-special-situations'));

        $this->assertNotNull($model);
        $this->assertSame('Crisis Management & Special Situations', $model->name);
    }

    public function test_save_with_an_original_slug_renames_the_record(): void
    {
        EngagementModelRecord::factory()->create(['slug' => 'old-slug', 'name' => 'Old Name']);

        $repository = new EloquentEngagementModelRepository;

        $repository->save(
            new EngagementModel(
                slug: Slug::fromString('new-slug'),
                name: 'New Name',
                summary: 'Renamed summary.',
                icon: 'target',
                position: 1,
            ),
            originalSlug: Slug::fromString('old-slug'),
        );

        $this->assertCount(1, EngagementModelRecord::all());
        $this->assertNull($repository->findBySlug(Slug::fromString('old-slug')));
        $this->assertNotNull($repository->findBySlug(Slug::fromString('new-slug')));
    }

    public function test_delete_removes_the_engagement_model(): void
    {
        EngagementModelRecord::factory()->create(['slug' => 'strategic-advisory-retainers']);

        $repository = new EloquentEngagementModelRepository;
        $repository->delete(Slug::fromString('strategic-advisory-retainers'));

        $this->assertNull($repository->findBySlug(Slug::fromString('strategic-advisory-retainers')));
    }
}
