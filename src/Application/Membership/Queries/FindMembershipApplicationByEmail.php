<?php

declare(strict_types=1);

namespace Application\Membership\Queries;

use Domain\Membership\Entities\MembershipApplication;
use Domain\Membership\Repositories\MembershipApplicationRepository;
use Domain\Shared\ValueObjects\EmailAddress;

/**
 * Backs the member account page: a member is identified by the email they
 * log in with, not by holding onto their application reference.
 */
final readonly class FindMembershipApplicationByEmail
{
    public function __construct(private MembershipApplicationRepository $applications) {}

    public function __invoke(string $email): ?MembershipApplication
    {
        return $this->applications->findByEmail(EmailAddress::fromString($email));
    }
}
