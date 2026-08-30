<?php

declare(strict_types=1);

namespace Application\Content\Queries;

use Domain\Content\Entities\PortfolioEngagement;
use Domain\Content\Repositories\PortfolioEngagementRepository;

final readonly class ListPortfolioEngagements
{
    public function __construct(private PortfolioEngagementRepository $engagements) {}

    /** @return list<PortfolioEngagement> */
    public function __invoke(): array
    {
        return $this->engagements->all();
    }
}
