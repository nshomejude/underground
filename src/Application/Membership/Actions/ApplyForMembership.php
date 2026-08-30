<?php

declare(strict_types=1);

// STUB: will be replaced by the real Membership domain module at merge time

namespace Application\Membership\Actions;

use Application\Membership\DataTransferObjects\MembershipApplicationPayload;
use Application\Membership\Queries\ListMembershipTiers;
use Application\Membership\Support\MembershipStubDirectory;
use Domain\Membership\Entities\MembershipApplication;
use Domain\Shared\Exceptions\DomainException;

final readonly class ApplyForMembership
{
    public function __construct(private ListMembershipTiers $tiers) {}

    public function __invoke(MembershipApplicationPayload $payload): MembershipApplication
    {
        if ($this->tiers->bySlug($payload->tier->value) === null) {
            throw new DomainException(sprintf(
                '"%s" is not a membership tier Underground extends.',
                $payload->tier->value,
            ));
        }

        $application = MembershipApplication::submit(
            tier: $payload->tier,
            name: $payload->name,
            organisation: $payload->organisation,
            email: $payload->email,
            phone: $payload->phone,
            country: $payload->country,
            statement: $payload->statement,
        );

        MembershipStubDirectory::remember($application);

        return $application;
    }
}
