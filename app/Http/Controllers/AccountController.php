<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\CertificateService;
use Application\Membership\Queries\FindMembershipApplicationByEmail;
use Application\Membership\Queries\ListMembershipTiers;
use Domain\Membership\Entities\MembershipApplication;
use Domain\Membership\ValueObjects\MembershipApplicationStatus;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

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
        $notifications = $this->notifications($request);

        if ($application === null) {
            return view('account.show', ['state' => 'none', 'notifications' => $notifications]);
        }

        if ($application->status() === MembershipApplicationStatus::Approved) {
            $certificate = app(CertificateService::class)->ensureFor($application);

            return view('account.show', [
                'state' => 'approved',
                ...$this->cardData($application),
                'certificate' => $certificate,
                'verifyUrl' => $certificate?->verificationUrl(),
                'serial' => $certificate?->serial,
                'timeline' => $this->timeline($application, true),
                'notifications' => $notifications,
            ]);
        }

        return view('account.show', [
            'state' => 'pending',
            'application' => $application,
            'timeline' => $this->timeline($application, false),
            'notifications' => $notifications,
        ]);
    }

    /** @return Collection<int, DatabaseNotification> */
    private function notifications(Request $request): Collection
    {
        if (! Schema::hasTable('notifications')) {
            return collect();
        }

        return $request->user()->notifications()->latest()->limit(6)->get();
    }

    /**
     * Journey steps. The approval date is the submission date for now, as
     * elsewhere in this controller (no separate approval timestamp exists).
     *
     * @return list<array{label: string, detail: string, state: string}>
     */
    private function timeline(MembershipApplication $application, bool $approved): array
    {
        $date = $application->submittedAt->format('j M Y');

        if ($approved) {
            return [
                ['label' => 'Applied', 'detail' => $date, 'state' => 'done'],
                ['label' => 'Under review', 'detail' => 'Completed', 'state' => 'done'],
                ['label' => 'Approved', 'detail' => $date, 'state' => 'done'],
                ['label' => 'Card issued', 'detail' => 'Valid until '.$application->submittedAt->modify('+1 year')->format('j M Y'), 'state' => 'now'],
            ];
        }

        $reviewing = $application->status() === MembershipApplicationStatus::UnderReview;

        return [
            ['label' => 'Applied', 'detail' => $date, 'state' => $reviewing ? 'done' : 'now'],
            ['label' => 'Under review', 'detail' => $reviewing ? 'In progress' : 'Pending', 'state' => $reviewing ? 'now' : 'todo'],
            ['label' => 'Approved', 'detail' => 'Awaiting decision', 'state' => 'todo'],
            ['label' => 'Card issued', 'detail' => 'After approval', 'state' => 'todo'],
        ];
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
