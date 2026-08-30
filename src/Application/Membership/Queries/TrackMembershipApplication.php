<?php

declare(strict_types=1);

// STUB: will be replaced by the real Membership domain module at merge time

namespace Application\Membership\Queries;

use Application\Membership\Support\MembershipStubDirectory;
use Domain\Membership\Entities\MembershipApplication;

final readonly class TrackMembershipApplication
{
    public function __invoke(string $reference): ?MembershipApplication
    {
        return MembershipStubDirectory::find($reference);
    }
}
