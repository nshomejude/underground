<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\MemberAccess;
use App\Services\PlanService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** The plan comparison: members (with upgrade requests) and the public marketing page. */
final class PlanController extends Controller
{
    public function __construct(
        private readonly PlanService $plans,
        private readonly MemberAccess $access,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $plans = $this->plans->activePlans();
        $rank = $this->access->tierRank($user);

        return view('plans.index', [
            'plans' => $plans,
            'rank' => $rank,
            'isMember' => $rank > 0,
            'canRequest' => $this->access->canUseNetwork($user),
            'current' => $this->plans->currentPlan($user),
            'pending' => $this->plans->pendingRequest($user),
            'service' => $this->plans,
            'matrix' => $this->plans->matrix($plans),
        ]);
    }

    public function publicIndex(): View
    {
        $plans = $this->plans->activePlans();

        return view('plans.public', [
            'plans' => $plans,
            'service' => $this->plans,
            'matrix' => $this->plans->matrix($plans),
        ]);
    }
}
