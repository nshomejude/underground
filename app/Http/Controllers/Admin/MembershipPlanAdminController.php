<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlanAdminRequest;
use App\Models\MembershipPlan;
use App\Models\PlanChangeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/** Admin CRUD for membership plans. */
final class MembershipPlanAdminController extends Controller
{
    public function index(): View
    {
        return view('admin.plans.index', [
            'plans' => MembershipPlan::query()->orderBy('position')->orderBy('id')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.plans.create', ['plan' => new MembershipPlan([
            'currency' => 'USD', 'billing_interval' => 'by_invitation', 'is_active' => true, 'position' => 0,
            'limits' => ['vote_rank' => 1, 'forum_access' => 'none', 'inquiry_response' => 'standard'],
        ])]);
    }

    public function store(PlanAdminRequest $request): RedirectResponse
    {
        MembershipPlan::create($request->planAttributes());

        return redirect()->route('admin.plans.index')->with('status', 'Plan created.');
    }

    public function edit(MembershipPlan $plan): View
    {
        return view('admin.plans.edit', ['plan' => $plan]);
    }

    public function update(PlanAdminRequest $request, MembershipPlan $plan): RedirectResponse
    {
        $plan->update($request->planAttributes());

        return redirect()->route('admin.plans.index')->with('status', 'Plan updated.');
    }

    public function destroy(MembershipPlan $plan): RedirectResponse
    {
        if (PlanChangeRequest::query()->where('to_plan_id', $plan->id)->where('status', 'pending')->exists()) {
            return redirect()->route('admin.plans.index')->withErrors(['plan' => 'This plan has pending requests. Deactivate it instead, or resolve the requests first.']);
        }

        $plan->delete();

        return redirect()->route('admin.plans.index')->with('status', 'Plan deleted.');
    }
}
