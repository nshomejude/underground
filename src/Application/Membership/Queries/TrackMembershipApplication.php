<?php

declare(strict_types=1);

namespace Application\Membership\Queries;

use Domain\Membership\Entities\MembershipApplication;
use Domain\Membership\Repositories\MembershipApplicationRepository;
use Domain\Membership\ValueObjects\MembershipReference;

final readonly class TrackMembershipApplication
{
    public function __construct(private MembershipApplicationRepository $applications) {}

    public function __invoke(string $reference): ?MembershipApplication
    {
        return $this->applications->findByReference(
            MembershipReference::fromString($reference),
        );
    }
}
