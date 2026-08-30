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

    /**
     * Create or update a sector. When $originalSlug is given, the record
     * matching it is updated in place (allowing the slug itself to change);
     * otherwise the sector's own slug is used, creating a new row if none
     * matches it yet.
     */
    public function save(Sector $sector, ?Slug $originalSlug = null): void;

    public function delete(Slug $slug): void;
}
