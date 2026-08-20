<?php

declare(strict_types=1);

namespace Domain\Insights\Repositories;

use Domain\Insights\Entities\Insight;
use Domain\Shared\ValueObjects\Slug;

interface InsightRepository
{
    /** @return list<Insight> most recently published first */
    public function published(?int $limit = null): array;

    public function findBySlug(Slug $slug): ?Insight;
}
