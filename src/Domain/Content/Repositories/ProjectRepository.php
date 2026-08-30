<?php

declare(strict_types=1);

namespace Domain\Content\Repositories;

use Domain\Content\Entities\Project;
use Domain\Shared\ValueObjects\Slug;

interface ProjectRepository
{
    /** @return list<Project> ordered by position */
    public function all(): array;

    public function findBySlug(Slug $slug): ?Project;

    /**
     * Create or update a project. When $originalSlug is given, the record
     * matching it is updated in place (allowing the slug itself to change);
     * otherwise the project's own slug is used, creating a new row if none
     * matches it yet.
     */
    public function save(Project $project, ?Slug $originalSlug = null): void;

    public function delete(Slug $slug): void;
}
