<?php

declare(strict_types=1);

namespace Application\Membership\Actions;

use Application\Membership\DataTransferObjects\MembershipApplicationPayload;
use Domain\Membership\Entities\MembershipApplication;
use Domain\Membership\Events\MembershipApplicationSubmitted;
use Domain\Membership\Repositories\MembershipApplicationRepository;
use Domain\Membership\Repositories\MembershipTierRepository;
use Domain\Shared\Exceptions\DomainException;
use Illuminate\Contracts\Events\Dispatcher;

final readonly class ApplyForMembership
{
    public function __construct(
        private MembershipTierRepository $tiers,
        private MembershipApplicationRepository $applications,
        private Dispatcher $events,
    ) {}

    public function __invoke(MembershipApplicationPayload $payload): MembershipApplication
    {
        if ($this->tiers->findBySlug($payload->tier) === null) {
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

        $this->applications->save($application);

        $this->events->dispatch(new MembershipApplicationSubmitted($application));

        return $application;
    }
}
