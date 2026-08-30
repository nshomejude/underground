<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Application\Membership\Queries\ListMembershipTiers;
use Application\Membership\Queries\TrackMembershipApplication;
use Domain\Membership\ValueObjects\MembershipApplicationStatus;
use Domain\Shared\Exceptions\DomainException;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Public, no-login status lookup for a membership application: the only
 * credential an applicant holds is the reference they were given on
 * submission, so this page trades that reference for status, tier, and
 * submitted date only — never the applicant's name, organisation, or
 * statement. An approved application does not reveal the membership card
 * here; that lives behind /account once the applicant registers or logs in.
 * See resources/views/membership/track.blade.php.
 */
final class MembershipTrackController extends Controller
{
    public function __construct(
        private readonly TrackMembershipApplication $track,
        private readonly ListMembershipTiers $tiers,
    ) {}

    public function show(Request $request): View
    {
        $reference = trim((string) $request->query('reference', ''));
        $application = null;
        $tier = null;
        $isApproved = false;
        $searched = $reference !== '';

        if ($searched) {
            try {
                $application = ($this->track)($reference);
            } catch (DomainException) {
                $application = null;
            }

            if ($application !== null) {
                $tier = $this->tiers->bySlug($application->tier->value);
                $isApproved = $application->status() === MembershipApplicationStatus::Approved;
            }
        }

        return view('membership.track', [
            'reference' => $reference,
            'application' => $application,
            'tier' => $tier,
            'isApproved' => $isApproved,
            'searched' => $searched,
        ]);
    }
}
