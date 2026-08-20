<?php

declare(strict_types=1);

namespace Domain\Content\Repositories;

use Domain\Content\Entities\Sector;
use Domain\Shared\ValueObjects\Slug;

interface SectorRepository
{
    /** @return list<Sector> ordered by position */
    public function all(): array;

    public function findBySlug(Slug $slug): ?Sector;
}
