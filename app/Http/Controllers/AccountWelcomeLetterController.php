<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\CertificateService;
use Application\Membership\Queries\FindMembershipApplicationByEmail;
use Application\Membership\Queries\ListMembershipTiers;
use Domain\Membership\ValueObjects\MembershipApplicationStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * The member's personalised welcome letter, printable and responsive.
 * Only for an approved member, and only ever the signed-in user's own.
 */
final class AccountWelcomeLetterController extends Controller
{
    public function __construct(
        private readonly FindMembershipApplicationByEmail $findApplication,
        private readonly ListMembershipTiers $tiers,
        private readonly CertificateService $certificates,
    ) {}

    public function __invoke(Request $request): Response
    {
        $application = ($this->findApplication)($request->user()->email);

        abort_if($application === null || $application->status() !== MembershipApplicationStatus::Approved, 404);

        $certificate = $this->certificates->ensureFor($application);

        abort_if($certificate === null || $certificate->status() === 'revoked', 404);

        $isOrganisation = $application->organisation !== null;

        return response()->view('account.welcome-letter', [
            'holder' => $isOrganisation ? $application->organisation : $application->name,
            'addressee' => $isOrganisation ? $application->name : $application->name,
            'isOrganisation' => $isOrganisation,
            'tierName' => $this->tiers->bySlug($application->tier->value)?->name ?? 'Member',
            'memberId' => (string) $application->memberId(),
            'issuedAt' => $certificate->issued_at,
            'validThrough' => $certificate->valid_through,
            'serial' => $certificate->serial,
        ])->withHeaders([
            'Cache-Control' => 'private, no-store',
            'X-Robots-Tag' => 'noindex, nofollow',
        ]);
    }
}
