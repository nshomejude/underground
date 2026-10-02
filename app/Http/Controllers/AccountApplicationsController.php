<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\CertificateService;
use Application\Membership\Queries\FindMembershipApplicationByEmail;
use Application\Membership\Queries\ListMembershipTiers;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * The member's membership application and where it stands.
 */
final class AccountApplicationsController extends Controller
{
    public function __construct(
        private readonly FindMembershipApplicationByEmail $findApplication,
        private readonly ListMembershipTiers $tiers,
        private readonly CertificateService $certificates,
    ) {}

    public function __invoke(Request $request): View
    {
        $application = ($this->findApplication)($request->user()->email);

        return view('account.applications', [
            'application' => $application,
            'tier' => $application === null ? null : $this->tiers->bySlug($application->tier->value),
            'certificate' => $application === null ? null : $this->certificates->ensureFor($application),
        ]);
    }
}
