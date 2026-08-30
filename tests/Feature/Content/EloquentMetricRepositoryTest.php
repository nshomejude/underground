<?php

declare(strict_types=1);

namespace Tests\Feature\Content;

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
}
