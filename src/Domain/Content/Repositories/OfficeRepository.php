<?php

declare(strict_types=1);

namespace Domain\Content\Repositories;

use Domain\Content\Entities\Office;
use Domain\Shared\ValueObjects\Slug;

interface OfficeRepository
{
    /** @return list<Office> ordered by position */
    public function all(): array;

    public function findBySlug(Slug $slug): ?Office;

    /**
     * Create or update an Office. When $originalSlug is given, the
     * record matching it is updated in place (allowing the slug itself
     * to change); otherwise the office's own slug is used, creating a
     * new row if none matches it yet.
     */
    public function save(Office $office, ?Slug $originalSlug = null): void;

    public function delete(Slug $slug): void;
}
