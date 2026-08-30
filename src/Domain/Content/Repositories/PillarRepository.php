<?php

declare(strict_types=1);

namespace Domain\Content\Repositories;

use Domain\Content\Entities\Pillar;
use Domain\Shared\ValueObjects\Slug;

interface PillarRepository
{
    /** @return list<Pillar> ordered by position */
    public function all(): array;

    public function findBySlug(Slug $slug): ?Pillar;

    /**
     * Create or update a pillar. When $originalSlug is given, the record
     * matching it is updated in place (allowing the slug itself to change);
     * otherwise the pillar's own slug is used, creating a new row if none
     * matches it yet.
     */
    public function save(Pillar $pillar, ?Slug $originalSlug = null): void;

    public function delete(Slug $slug): void;
}
