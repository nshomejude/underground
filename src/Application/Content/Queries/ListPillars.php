<?php

declare(strict_types=1);

namespace Application\Content\Queries;

use Domain\Content\Entities\Pillar;
use Domain\Content\Repositories\PillarRepository;

final readonly class ListPillars
{
    public function __construct(private PillarRepository $pillars)
    {
    }

    /** @return list<Pillar> */
    public function __invoke(): array
    {
        return $this->pillars->all();
    }
}
