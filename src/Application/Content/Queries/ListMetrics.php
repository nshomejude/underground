<?php

declare(strict_types=1);

namespace Application\Content\Queries;

use Domain\Content\Entities\Metric;
use Domain\Content\Repositories\MetricRepository;

final readonly class ListMetrics
{
    public function __construct(private MetricRepository $metrics) {}

    /** @return list<Metric> */
    public function __invoke(): array
    {
        return $this->metrics->all();
    }
}
