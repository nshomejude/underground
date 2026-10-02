<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\CertificateService;
use Application\Membership\Queries\FindMembershipApplicationByEmail;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Every document the member can open or share: certificate, card,
 * verification link, application reference and the legal terms.
 */
final class AccountDocumentsController extends Controller
{
    public function __construct(
        private readonly FindMembershipApplicationByEmail $findApplication,
        private readonly CertificateService $certificates,
    ) {}

    public function __invoke(Request $request): View
    {
        $application = ($this->findApplication)($request->user()->email);

        return view('account.documents', [
            'application' => $application,
            'certificate' => $application === null ? null : $this->certificates->ensureFor($application),
        ]);
    }
}
