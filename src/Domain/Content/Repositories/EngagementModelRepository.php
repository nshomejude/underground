<?php

declare(strict_types=1);

namespace Domain\Content\Repositories;

use Domain\Content\Entities\EngagementModel;
use Domain\Shared\ValueObjects\Slug;

interface EngagementModelRepository
{
    /** @return list<EngagementModel> ordered by position */
    public function all(): array;

    public function findBySlug(Slug $slug): ?EngagementModel;

    /**
     * Create or update an engagement model. When $originalSlug is given, the
     * record matching it is updated in place (allowing the slug itself to
     * change); otherwise the model's own slug is used, creating a new row if
     * none matches it yet.
     */
    public function save(EngagementModel $model, ?Slug $originalSlug = null): void;

    public function delete(Slug $slug): void;
}
