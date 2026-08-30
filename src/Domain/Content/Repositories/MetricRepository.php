<?php

declare(strict_types=1);

namespace Domain\Content\Repositories;

use Domain\Content\Entities\Metric;
use Domain\Shared\ValueObjects\Slug;

interface MetricRepository
{
    /** @return list<Metric> ordered by position */
    public function all(): array;

    public function findBySlug(Slug $slug): ?Metric;

    /**
     * Create or update a metric. When $originalSlug is given, the record
     * matching it is updated in place (allowing the slug itself to change);
     * otherwise the metric's own slug is used, creating a new row if none
     * matches it yet.
     */
    public function save(Metric $metric, ?Slug $originalSlug = null): void;

    public function delete(Slug $slug): void;
}
