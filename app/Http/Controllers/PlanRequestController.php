<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PlanChangeFormRequest;
use App\Models\MembershipPlan;
use App\Models\PlanChangeRequest;
use App\Services\PlanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** A member's plan change requests: send, list and withdraw. */
final class PlanRequestController extends Controller
{
    public function __construct(private readonly PlanService $plans) {}

    public function store(PlanChangeFormRequest $request): RedirectResponse
    {
        $target = MembershipPlan::query()->findOrFail($request->integer('plan_id'));

        $this->plans->submit($request->user(), $target, $request->validated('note'), $request->filled('amount') ? (int) round(((float) $request->input('amount')) * 100) : null);

        return redirect()->route('plans.requests')->with('status', 'Your request was sent. We have emailed you a receipt.');
    }

    public function index(Request $request): View
    {
        return view('plans.requests', [
            'requests' => PlanChangeRequest::query()
                ->where('user_id', $request->user()->id)
                ->with(['toPlan', 'fromPlan'])
                ->latest('id')
                ->get(),
        ]);
    }

    public function withdraw(Request $request, PlanChangeRequest $planChangeRequest): RedirectResponse
    {
        abort_unless($planChangeRequest->user_id === $request->user()->id, 404);

        $this->plans->withdraw($planChangeRequest);

        return redirect()->route('plans.requests')->with('status', 'Your request was withdrawn.');
    }
}
