<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlanDecisionRequest;
use App\Models\PlanChangeRequest;
use App\Services\PlanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Staff review of plan change requests. Approving records the decision and
 * emails the member; it now upgrades the member automatically; staff no longer do that
 * from Membership applications.
 */
final class PlanRequestAdminController extends Controller
{
    public function __construct(private readonly PlanService $plans) {}

    public function index(Request $request): View
    {
        $status = $request->query('status', 'pending');

        return view('admin.plan-requests.index', [
            'requests' => PlanChangeRequest::query()
                ->with(['user', 'toPlan', 'fromPlan'])
                ->when(in_array($status, ['pending', 'approved', 'declined', 'withdrawn'], true), fn ($q) => $q->where('status', $status))
                ->latest('id')
                ->get(),
            'status' => $status,
        ]);
    }

    public function show(PlanChangeRequest $planChangeRequest): View
    {
        return view('admin.plan-requests.show', ['planRequest' => $planChangeRequest->load(['user', 'toPlan', 'fromPlan', 'reviewer'])]);
    }

    public function approve(PlanDecisionRequest $request, PlanChangeRequest $planChangeRequest): RedirectResponse
    {
        return $this->decide($request, $planChangeRequest, true);
    }

    public function decline(PlanDecisionRequest $request, PlanChangeRequest $planChangeRequest): RedirectResponse
    {
        return $this->decide($request, $planChangeRequest, false);
    }

    private function decide(PlanDecisionRequest $request, PlanChangeRequest $planChangeRequest, bool $approve): RedirectResponse
    {
        if (! $planChangeRequest->isPending()) {
            return redirect()->route('admin.plan-requests.show', $planChangeRequest)->withErrors(['response_note' => 'This request has already been closed.']);
        }

        $this->plans->decide($planChangeRequest, $request->user(), $approve, $request->validated('response_note'));

        return redirect()->route('admin.plan-requests.show', $planChangeRequest)
            ->with('status', $approve ? 'Request approved: the member was upgraded automatically and emailed.' : 'Request declined and the member was emailed.');
    }
}
