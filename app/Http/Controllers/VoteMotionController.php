<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\MotionRequest;
use App\Models\Motion;
use App\Services\MemberAccess;
use App\Services\MotionException;
use App\Services\MotionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/** Member side: opening, editing, publishing and withdrawing a motion. */
final class VoteMotionController extends Controller
{
    public function __construct(
        private readonly MotionService $motions,
        private readonly MemberAccess $access,
    ) {}

    public function create(Request $request): View
    {
        abort_unless($this->access->canCreateMotion($request->user()), 403);

        return view('votes.create', ['motion' => new Motion([
            'kind' => 'decision', 'min_tier_rank' => 1, 'quorum' => 10, 'pass_threshold' => 50,
            'allow_change' => true, 'show_results' => 'after_close',
        ])]);
    }

    public function store(MotionRequest $request): RedirectResponse
    {
        try {
            $motion = $this->motions->create($request->user(), $request->motionData(), $request->wantsToPublish());
        } catch (MotionException $e) {
            return back()->withInput()->withErrors(['closes_at' => $e->getMessage()]);
        }

        return redirect()->route('votes.show', $motion)->with('status', $motion->isDraft() ? 'Draft saved.' : 'The motion has been published.');
    }

    public function edit(Request $request, Motion $motion): View
    {
        abort_unless($this->motions->canEdit($request->user(), $motion), 403);

        return view('votes.create', ['motion' => $motion]);
    }

    public function update(MotionRequest $request, Motion $motion): RedirectResponse
    {
        abort_unless($this->motions->canEdit($request->user(), $motion), 403);

        try {
            $this->motions->update($motion, $request->user(), $request->motionData());
            if ($request->wantsToPublish() && $motion->isDraft()) {
                $this->motions->publish($motion, $request->user());
            }
        } catch (MotionException $e) {
            return back()->withInput()->withErrors(['closes_at' => $e->getMessage()]);
        }

        return redirect()->route('votes.show', $motion)->with('status', 'Motion saved.');
    }

    public function publish(Request $request, Motion $motion): RedirectResponse
    {
        abort_unless($request->user()->is_admin || $motion->created_by === $request->user()->id, 403);

        try {
            $this->motions->publish($motion, $request->user());
        } catch (MotionException $e) {
            return back()->withErrors(['vote' => $e->getMessage()]);
        }

        return redirect()->route('votes.show', $motion)->with('status', 'The motion has been published.');
    }

    public function cancel(Request $request, Motion $motion): RedirectResponse
    {
        abort_unless($this->motions->canCancel($request->user(), $motion), 403);

        $request->validate(['reason' => ['nullable', 'string', 'max:300']]);

        try {
            $this->motions->cancel($motion, $request->user(), $request->input('reason'));
        } catch (MotionException $e) {
            return back()->withErrors(['vote' => $e->getMessage()]);
        }

        return redirect()->route('votes.index')->with('status', 'The motion has been cancelled.');
    }
}
