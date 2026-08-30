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
            ->map($this->toEntity(...))
            ->all();
    }

    public function findBySlug(Slug $slug): ?Metric
    {
        $record = MetricRecord::query()->where('slug', $slug->value)->first();

        return $record === null ? null : $this->toEntity($record);
    }

    public function save(Metric $metric, ?Slug $originalSlug = null): void
    {
        MetricRecord::query()->updateOrCreate(
            ['slug' => ($originalSlug ?? $metric->slug)->value],
            [
                'slug' => $metric->slug->value,
                'value' => $metric->value,
                'label' => $metric->label,
                'icon' => $metric->icon,
                'position' => $metric->position,
            ],
        );
    }

    public function delete(Slug $slug): void
    {
        MetricRecord::query()->where('slug', $slug->value)->delete();
    }

    private function toEntity(MetricRecord $record): Metric
    {
        return new Metric(
            slug: Slug::fromString($record->slug),
            value: $record->value,
            label: $record->label,
            icon: $record->icon,
            position: $record->position,
        );
    }
}
