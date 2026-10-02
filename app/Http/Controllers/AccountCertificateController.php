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
 * The member's print-ready certificate of membership. Looked up only from
 * the signed-in user's own email, so one member can never see another's.
 * Available only for an approved application whose certificate is not revoked.
 */
final class AccountCertificateController extends Controller
{
    public function __construct(
        private readonly FindMembershipApplicationByEmail $findApplication,
        private readonly ListMembershipTiers $tiers,
        private readonly CertificateService $certificates,
    ) {}

    public function show(Request $request): Response
    {
        $application = ($this->findApplication)($request->user()->email);

        abort_if($application === null || $application->status() !== MembershipApplicationStatus::Approved, 404);

        $certificate = $this->certificates->ensureFor($application);

        abort_if($certificate === null || $certificate->status() === 'revoked', 404);

        $isOrganisation = $application->organisation !== null;

        return response()->view('account.certificate', [
            'certificate' => $certificate,
            'holder' => $isOrganisation ? $application->organisation : $application->name,
            'tierName' => $this->tiers->bySlug($application->tier->value)?->name ?? 'Member',
            'memberId' => (string) $application->memberId(),
        ])->withHeaders([
            'Cache-Control' => 'private, no-store',
            'X-Robots-Tag' => 'noindex, nofollow',
        ]);
    }
}
