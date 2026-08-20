<?php

declare(strict_types=1);

namespace Application\Content\Queries;

use Domain\Content\Entities\Capability;
use Domain\Content\Repositories\CapabilityRepository;
use Domain\Shared\ValueObjects\Slug;

final readonly class ListCapabilities
{
    public function __construct(private CapabilityRepository $capabilities)
    {
    }

    /** @return list<Capability> */
    public function __invoke(bool $featuredOnly = false, ?int $limit = null): array
    {
        return $featuredOnly
            ? $this->capabilities->featured($limit)
            : array_slice($this->capabilities->all(), 0, $limit ?? PHP_INT_MAX);
    }

    public function bySlug(string $slug): ?Capability
    {
        return $this->capabilities->findBySlug(Slug::fromString($slug));
    }
}
