<?php

declare(strict_types=1);

namespace Domain\Content\Entities;

use Domain\Content\ValueObjects\EmploymentStatus;
use Domain\Content\ValueObjects\TeamRoleCategory;
use Domain\Shared\ValueObjects\Slug;

/**
 * A named principal shown on the /team pages. The founder gets a real
 * portrait (icon is ignored when hasPortrait is true); every other
 * principal is rendered with an icon avatar instead.
 *
 * roleCategory separates GOVERNANCE standing (Board Members, Advisory
 * Board) from OPERATIONAL standing (Executive, Regional, Expertise) — see
 * Domain\Content\ValueObjects\TeamRoleCategory. employmentStatus records
 * whether the firm engages this person directly or on an independent
 * basis, so the site never implies a staffing relationship that does not
 * exist.
 */
final readonly class TeamMember
{
    public function __construct(
        public Slug $slug,
        public string $name,
        public string $title,
        public string $background,
        public ?string $icon,
        public bool $hasPortrait,
        public TeamRoleCategory $roleCategory,
        public EmploymentStatus $employmentStatus,
        /** @var list<string> */
        public array $languages,
        public int $position,
    ) {}

    /**
     * Governance members are split between "Board Members" and the
     * "Advisory Board" by their title text — the roster only tracks the
     * broader Governance category, not a fifth enum case, so this is the
     * one place that distinction is drawn.
     */
    public function isAdvisoryBoard(): bool
    {
        return $this->roleCategory === TeamRoleCategory::Governance
            && str_contains(strtolower($this->title), 'advisory');
    }

    public function isBoardMember(): bool
    {
        return $this->roleCategory === TeamRoleCategory::Governance && ! $this->isAdvisoryBoard();
    }

    public function toArray(): array
    {
        return [
            'slug' => $this->slug->value,
            'name' => $this->name,
            'title' => $this->title,
            'background' => $this->background,
            'icon' => $this->icon,
            'portrait' => $this->hasPortrait,
            'role_category' => $this->roleCategory->value,
            'role_category_label' => $this->roleCategory->label(),
            'employment_status' => $this->employmentStatus->value,
            'employment_status_label' => $this->employmentStatus->label(),
            'languages' => $this->languages,
            'position' => $this->position,
        ];
    }
}
