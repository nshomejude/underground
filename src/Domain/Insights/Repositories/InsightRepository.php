<?php

declare(strict_types=1);

namespace Domain\Insights\Repositories;

use Domain\Insights\Entities\Insight;
use Domain\Shared\ValueObjects\Slug;

interface InsightRepository
{
    /** @return list<Insight> most recently published first */
    public function published(?int $limit = null): array;

    /** @return list<Insight> every insight, published or not, most recently created first */
    public function all(): array;

    public function findBySlug(Slug $slug): ?Insight;

    public function save(Insight $insight): void;

    public function delete(Slug $slug): void;
}
