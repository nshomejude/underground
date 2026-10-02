<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\MotionVoteRequest;
use App\Models\Motion;
use App\Models\Vote;
use App\Services\MemberAccess;
use App\Services\MotionException;
use App\Services\MotionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/** Member side of internal voting: list, read, cast. */
final class VoteController extends Controller
{
    public function __construct(
        private readonly MotionService $motions,
        private readonly MemberAccess $access,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $this->motions->closeDue();

        $rank = $user->is_admin ? 3 : $this->access->tierRank($user);
        $eligible = fn () => Motion::query()->where('min_tier_rank', '<=', $rank)->withCount('votes');

        $open = $eligible()->where('status', Motion::OPEN)->where('opens_at', '<=', now())->where('closes_at', '>', now())
            ->orderBy('closes_at')->get();

        $decided = $eligible()->where(function ($q): void {
            $q->whereIn('status', [Motion::PASSED, Motion::REJECTED, Motion::CLOSED])
                ->orWhere(fn ($c) => $c->where('status', Motion::CANCELLED)->whereNotNull('published_at'));
        })->latest('closed_at')->latest('id')->limit(30)->get();

        $mine = Motion::query()->withCount('votes')->where('created_by', $user->id)
            ->where(function ($q): void {
                $q->where('status', Motion::DRAFT)->orWhere(fn ($c) => $c->where('status', Motion::OPEN)->where('opens_at', '>', now()));
            })->latest()->get();

        $myVotes = Vote::query()->where('user_id', $user->id)
            ->whereIn('motion_id', $open->pluck('id')->merge($decided->pluck('id')))
            ->pluck('choice', 'motion_id');

        return view('votes.index', [
            'open' => $open,
            'decided' => $decided,
            'mine' => $mine,
            'myVotes' => $myVotes,
            'canCreate' => $this->access->canCreateMotion($user),
        ]);
    }

    public function show(Request $request, Motion $motion): View
    {
        $user = $request->user();
        $this->motions->closeIfDue($motion);
        $motion->refresh();

        abort_unless($this->motions->canView($user, $motion), 404);

        $myVote = Vote::query()->where('motion_id', $motion->id)->where('user_id', $user->id)->first();
        $showResults = $this->motions->resultsVisible($motion);
        $tally = null;
        if ($showResults) {
            $tally = $motion->isDecided() && is_array($motion->result) ? $motion->result : $this->motions->tally($motion);
        }

        $voters = collect();
        if ($this->motions->canSeeVoters($user, $motion)) {
            $voters = $motion->votes()->with('user:id,name')->orderBy('id')->get();
        }

        return view('votes.show', [
            'motion' => $motion->loadCount('votes')->load('creator:id,name'),
            'myVote' => $myVote,
            'tally' => $tally,
            'voters' => $voters,
            'canVote' => $this->motions->canVote($user, $motion) && $motion->isOpenNow(),
            'canEdit' => $this->motions->canEdit($user, $motion),
            'canCancel' => $this->motions->canCancel($user, $motion),
            'isCreator' => $motion->created_by === $user->id,
            'rules' => MotionService::rulesSentence($motion),
            'body' => MotionService::bodyBlocks($motion->body),
        ]);
    }

    public function cast(MotionVoteRequest $request, Motion $motion): RedirectResponse
    {
        try {
            $this->motions->castVote($motion, $request->user(), (string) $request->input('choice'), $request->input('comment'));
        } catch (MotionException $e) {
            return redirect()->route('votes.show', $motion)->withErrors(['vote' => $e->getMessage()]);
        }

        return redirect()->route('votes.show', $motion)->with('status', 'Your vote has been recorded. Thank you.');
    }
}
