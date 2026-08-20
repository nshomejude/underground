<?php

declare(strict_types=1);

namespace Application\Insights\Queries;

use Domain\Insights\Entities\Insight;
use Domain\Insights\Repositories\InsightRepository;
use Domain\Shared\ValueObjects\Slug;

final readonly class ListInsights
{
    public function __construct(private InsightRepository $insights)
    {
    }

    /** @return list<Insight> */
    public function __invoke(?int $limit = null): array
    {
        return $this->insights->published($limit);
    }

    public function bySlug(string $slug): ?Insight
    {
        return $this->insights->findBySlug(Slug::fromString($slug));
    }
}
