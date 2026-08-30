<?php

declare(strict_types=1);

namespace Tests\Feature\Content;

use Domain\Shared\ValueObjects\Slug;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Models\CapabilityRecord;
use Infrastructure\Persistence\Eloquent\Repositories\EloquentCapabilityRepository;
use Tests\TestCase;

final class EloquentCapabilityRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_returns_capabilities_ordered_by_position(): void
    {
        CapabilityRecord::factory()->create(['slug' => 'media-narrative-management', 'position' => 4]);
        CapabilityRecord::factory()->create(['slug' => 'government-political-affairs', 'position' => 1]);
        CapabilityRecord::factory()->create(['slug' => 'strategic-intelligence-analysis', 'position' => 2]);
        CapabilityRecord::factory()->create(['slug' => 'investment-capital-strategy', 'position' => 3]);

        $repository = new EloquentCapabilityRepository;

        $this->assertSame(
            [
                'government-political-affairs',
                'strategic-intelligence-analysis',
                'investment-capital-strategy',
                'media-narrative-management',
            ],
            array_map(fn ($capability) => $capability->slug->value, $repository->all()),
        );
    }

    public function test_featured_returns_only_featured_capabilities_ordered_by_position(): void
    {
        CapabilityRecord::factory()->create(['slug' => 'featured-one', 'position' => 2, 'is_featured' => true]);
        CapabilityRecord::factory()->create(['slug' => 'not-featured', 'position' => 1, 'is_featured' => false]);
        CapabilityRecord::factory()->create(['slug' => 'featured-two', 'position' => 1, 'is_featured' => true]);

        $repository = new EloquentCapabilityRepository;

        $this->assertSame(
            ['featured-two', 'featured-one'],
            array_map(fn ($capability) => $capability->slug->value, $repository->featured()),
        );
    }

    public function test_featured_respects_the_limit(): void
    {
        CapabilityRecord::factory()->create(['slug' => 'featured-one', 'position' => 1, 'is_featured' => true]);
        CapabilityRecord::factory()->create(['slug' => 'featured-two', 'position' => 2, 'is_featured' => true]);

        $repository = new EloquentCapabilityRepository;

        $this->assertCount(1, $repository->featured(1));
    }

    public function test_find_by_slug_returns_the_matching_capability(): void
    {
        CapabilityRecord::factory()->create([
            'slug' => 'government-political-affairs',
            'title' => 'Government & Political Affairs',
            'summary' => 'Discreet counsel at the intersection of policy and power.',
            'icon' => 'landmark',
            'position' => 1,
            'is_featured' => true,
        ]);

        $repository = new EloquentCapabilityRepository;

        $capability = $repository->findBySlug(Slug::fromString('government-political-affairs'));

        $this->assertNotNull($capability);
        $this->assertSame('Government & Political Affairs', $capability->title);
        $this->assertSame(['Government &', 'Political Affairs'], $capability->titleLines());
        $this->assertTrue($capability->isFeatured);
    }

    public function test_find_by_slug_returns_null_for_a_missing_slug(): void
    {
        $repository = new EloquentCapabilityRepository;

        $this->assertNull($repository->findBySlug(Slug::fromString('does-not-exist')));
    }
}
