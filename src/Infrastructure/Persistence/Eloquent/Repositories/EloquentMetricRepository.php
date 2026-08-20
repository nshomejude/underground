<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Repositories;

use Domain\Content\Entities\Metric;
use Domain\Content\Repositories\MetricRepository;
use Domain\Shared\ValueObjects\Slug;
use Infrastructure\Persistence\Eloquent\Models\MetricRecord;

final class EloquentMetricRepository implements MetricRepository
{
    public function all(): array
    {
        return MetricRecord::query()
            ->orderBy('position')
            ->get()
            ->map(fn (MetricRecord $record): Metric => new Metric(
                slug: Slug::fromString($record->slug),
                value: $record->value,
                label: $record->label,
                icon: $record->icon,
                position: $record->position,
            ))
            ->all();
    }
}
