<?php

declare(strict_types=1);

namespace Domain\Content\Repositories;

use Domain\Content\Entities\Pillar;

interface PillarRepository
{
    /** @return list<Pillar> ordered by position */
    public function all(): array;
}
