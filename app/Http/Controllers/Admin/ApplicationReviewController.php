<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Application\Membership\Actions\ApproveMembershipApplication;
use Application\Membership\Actions\DeclineMembershipApplication;
use Application\Membership\Queries\ListMembershipApplications;
use Domain\Membership\Entities\MembershipApplication;
use Domain\Membership\Repositories\MembershipApplicationRepository;
use Domain\Membership\ValueObjects\MembershipApplicationStatus;
use Domain\Membership\ValueObjects\MembershipReference;
use Domain\Shared\Exceptions\DomainException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * The staff-facing review queue for membership applications: approve or
 * decline whatever is waiting. Every application starts life as Submitted
 * and must pass through UnderReview before it can be Approved or Declined
 * (see MembershipApplicationStatus::allowedTransitions()) — a first click
 * here quietly advances a Submitted application into UnderReview before
 * applying the requested decision, so staff never has to think about that
 * intermediate state.
 */
final class ApplicationReviewController extends Controller
{
    public function __construct(
        private readonly ListMembershipApplications $list,
        private readonly MembershipApplicationRepository $applications,
        private readonly ApproveMembershipApplication $approve,
        private readonly DeclineMembershipApplication $decline,
    ) {}

    public function index(): View
    {
        return view('admin.applications.index', [
            'applications' => ($this->list)(),
        ]);
    }

    public function approve(string $reference): RedirectResponse
    {
        $application = $this->findOrFail($reference);

        try {
            $this->advanceToUnderReview($application);
            ($this->approve)($application);
        } catch (DomainException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('status', sprintf(
            'Application %s approved — member id %s issued.',
            $application->reference->value,
            $application->memberId(),
        ));
    }

    public function decline(string $reference): RedirectResponse
    {
        $application = $this->findOrFail($reference);

        try {
            $this->advanceToUnderReview($application);
            ($this->decline)($application);
        } catch (DomainException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('status', sprintf('Application %s declined.', $application->reference->value));
    }

    private function findOrFail(string $reference): MembershipApplication
    {
        $application = $this->applications->findByReference(MembershipReference::fromString($reference));

        abort_if($application === null, 404);

        return $application;
    }

    private function advanceToUnderReview(MembershipApplication $application): void
    {
        if ($application->status() === MembershipApplicationStatus::Submitted) {
            $application->transitionTo(MembershipApplicationStatus::UnderReview);
        }
    }
}
