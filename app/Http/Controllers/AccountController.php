<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Application\Membership\Queries\FindMembershipApplicationByEmail;
use Application\Membership\Queries\ListMembershipTiers;
use Domain\Membership\Entities\MembershipApplication;
use Domain\Membership\ValueObjects\MembershipApplicationStatus;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * The member's private account area: the physical card once an application
 * is approved, a tracking pointer while it is still under review, or an
 * invitation to apply if this account has never applied at all. There is
 * no staff-facing approval flow here — see ApproveMembershipApplication.
 */
final class AccountController extends Controller
{
    public function __construct(
        private readonly FindMembershipApplicationByEmail $findApplication,
        private readonly ListMembershipTiers $tiers,
    ) {}

    public function show(Request $request): View
    {
        $user = $request->user();

        $application = ($this->findApplication)($user->email);

        if ($application === null) {
            return view('account.show', ['state' => 'none']);
        }

        if ($application->status() === MembershipApplicationStatus::Approved) {
            return view('account.show', [
                'state' => 'approved',
                ...$this->cardData($application),
            ]);
        }

        return view('account.show', [
            'state' => 'pending',
            'application' => $application,
        ]);
    }

    /** @return array<string, mixed> */
    private function cardData(MembershipApplication $application): array
    {
        $tier = $this->tiers->bySlug($application->tier->value);

        $isOrganisation = $application->organisation !== null;

        // The permanent member id's year is fixed at first issuance; the
        // card's own issued/valid-through cycle renews independently — a
        // year from the date membership was granted (submission is the
        // closest timestamp the aggregate carries to that event today).
        $issuedOn = $application->submittedAt;
        $validThrough = $issuedOn->modify('+1 year');

        return [
            'application' => $application,
            'variant' => $isOrganisation ? 'organisation' : 'individual',
            'name' => $isOrganisation ? $application->organisation : $application->name,
            'representative' => $isOrganisation ? $application->name : null,
            'representativeTitle' => null,
            'tier' => $tier,
            'memberId' => (string) $application->memberId(),
            'issuedOn' => $issuedOn,
            'validThrough' => $validThrough,
        ];
    }
}
