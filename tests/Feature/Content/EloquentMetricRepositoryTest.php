<?php

declare(strict_types=1);

namespace Tests\Feature\Content;

use Domain\Content\Entities\Metric;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Models\MetricRecord;
use Infrastructure\Persistence\Eloquent\Repositories\EloquentMetricRepository;
use Tests\TestCase;

final class EloquentMetricRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_returns_metrics_ordered_by_position(): void
    {
        MetricRecord::factory()->create(['slug' => 'countries-served', 'position' => 3]);
        MetricRecord::factory()->create(['slug' => 'government-relationships', 'position' => 1]);
        MetricRecord::factory()->create(['slug' => 'projects-supported', 'position' => 2]);

        $repository = new EloquentMetricRepository;

        $this->assertSame(
            ['government-relationships', 'projects-supported', 'countries-served'],
            array_map(fn ($metric) => $metric->slug->value, $repository->all()),
        );
    }

    public function test_all_round_trips_the_metric_fields(): void
    {
        MetricRecord::factory()->create([
            'slug' => 'government-relationships',
            'value' => '250+',
            'label' => 'government relationships | worldwide',
            'icon' => 'globe',
            'position' => 1,
        ]);

        $repository = new EloquentMetricRepository;

        $metric = $repository->all()[0];

        $this->assertSame('250+', $metric->value);
        $this->assertSame('globe', $metric->icon);
        $this->assertSame(['government relationships', 'worldwide'], $metric->labelLines());
    }

    public function test_find_by_slug_returns_null_for_a_missing_slug(): void
    {
        $repository = new EloquentMetricRepository;

        $this->assertNull($repository->findBySlug(Slug::fromString('does-not-exist')));
    }

    public function test_save_creates_a_new_metric(): void
    {
        $repository = new EloquentMetricRepository;

        $repository->save(new Metric(
            slug: Slug::fromString('countries-served'),
            value: '90+',
            label: 'countries served',
            icon: 'globe',
            position: 4,
        ));

        $metric = $repository->findBySlug(Slug::fromString('countries-served'));

        $this->assertNotNull($metric);
        $this->assertSame('90+', $metric->value);
    }

    public function test_save_updates_an_existing_metric_in_place(): void
    {
        MetricRecord::factory()->create(['slug' => 'countries-served', 'value' => '80+']);

        $repository = new EloquentMetricRepository;

        $repository->save(new Metric(
            slug: Slug::fromString('countries-served'),
            value: '90+',
            label: 'countries served',
            icon: 'globe',
            position: 1,
        ));

        $this->assertCount(1, MetricRecord::all());
        $this->assertSame('90+', $repository->findBySlug(Slug::fromString('countries-served'))->value);
    }

    public function test_delete_removes_the_metric(): void
    {
        MetricRecord::factory()->create(['slug' => 'countries-served']);

        $repository = new EloquentMetricRepository;
        $repository->delete(Slug::fromString('countries-served'));

        $this->assertNull($repository->findBySlug(Slug::fromString('countries-served')));
    }
}
