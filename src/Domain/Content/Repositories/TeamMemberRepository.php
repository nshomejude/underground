<?php

declare(strict_types=1);

namespace Domain\Content\Repositories;

use Domain\Content\Entities\TeamMember;
use Domain\Shared\ValueObjects\Slug;

interface TeamMemberRepository
{
    /** @return list<TeamMember> ordered by position */
    public function all(): array;

    public function findBySlug(Slug $slug): ?TeamMember;

    /**
     * Create or update a team member. When $originalSlug is given, the
     * record matching it is updated in place (allowing the slug itself to
     * change); otherwise the member's own slug is used, creating a new row
     * if none matches it yet.
     */
    public function save(TeamMember $member, ?Slug $originalSlug = null): void;

    public function delete(Slug $slug): void;
}
