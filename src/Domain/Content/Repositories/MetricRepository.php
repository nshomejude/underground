<?php

declare(strict_types=1);

namespace Domain\Content\Repositories;

use Domain\Content\Entities\Metric;

interface MetricRepository
{
    /** @return list<Metric> ordered by position */
    public function all(): array;
}
