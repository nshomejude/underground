<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MotionRequest;
use App\Models\Motion;
use App\Services\MotionException;
use App\Services\MotionService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class MotionAdminController extends Controller
{
    public function __construct(private readonly MotionService $motions) {}

    public function index(Request $request): View
    {
        $this->motions->closeDue();

        $query = $this->filtered($request)->withCount('votes')->with('creator:id,name')->latest('id');

        return view('admin.motions.index', [
            'motions' => $query->paginate(25)->withQueryString(),
            'filters' => $request->only(['status', 'kind', 'q']),
            'statuses' => [Motion::DRAFT, Motion::OPEN, Motion::PASSED, Motion::REJECTED, Motion::CLOSED, Motion::CANCELLED],
        ]);
    }

    public function create(): View
    {
        return view('admin.motions.create', ['motion' => new Motion([
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

        return redirect()->route('admin.motions.show', $motion)->with('status', 'Motion saved.');
    }

    public function show(Request $request, Motion $motion): View
    {
        $this->motions->closeIfDue($motion);
        $motion->refresh()->loadCount('votes')->load('creator:id,name');

        return view('admin.motions.show', [
            'motion' => $motion,
            'tally' => $motion->isDecided() && is_array($motion->result) ? $motion->result : null,
            'events' => $motion->events()->with('user:id,name')->orderBy('id')->get(),
            'voters' => $this->motions->canSeeVoters($request->user(), $motion)
                ? $motion->votes()->with('user:id,name')->orderBy('id')->get() : collect(),
            'rules' => MotionService::rulesSentence($motion),
            'canCancel' => $this->motions->canCancel($request->user(), $motion),
            'canClose' => $motion->isOpenNow(),
        ]);
    }

    public function cancel(Request $request, Motion $motion): RedirectResponse
    {
        $data = $request->validate(['reason' => ['required', 'string', 'min:3', 'max:300']]);

        try {
            $this->motions->cancel($motion, $request->user(), $data['reason']);
        } catch (MotionException $e) {
            return back()->withErrors(['reason' => $e->getMessage()]);
        }

        return redirect()->route('admin.motions.show', $motion)->with('status', 'Motion cancelled.');
    }

    public function close(Request $request, Motion $motion): RedirectResponse
    {
        $data = $request->validate(['reason' => ['required', 'string', 'min:3', 'max:300']]);

        try {
            $this->motions->closeEarly($motion, $request->user(), $data['reason']);
        } catch (MotionException $e) {
            return back()->withErrors(['reason' => $e->getMessage()]);
        }

        return redirect()->route('admin.motions.show', $motion)->with('status', 'Motion closed and the result recorded.');
    }

    /** Result summary only: never individual votes. */
    public function export(Request $request): StreamedResponse
    {
        $this->motions->closeDue();
        $motions = $this->filtered($request)->withCount('votes')->orderBy('id')->get();

        return response()->streamDownload(function () use ($motions): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Title', 'Kind', 'Status', 'Outcome', 'Minimum tier', 'Anonymous', 'Weighted', 'Quorum', 'Threshold %', 'Voters', 'Counts', 'Opens', 'Closes', 'Closed at']);
            foreach ($motions as $m) {
                $counts = collect($m->result['counts'] ?? [])->map(fn ($n, $c) => $c.': '.$n)->implode('; ');
                fputcsv($out, [
                    $m->id, $this->safe($m->title), $m->kind, $m->status, $m->outcome, $m->tierLabel(),
                    $m->anonymous ? 'yes' : 'no', $m->weighted ? 'yes' : 'no', $m->quorum,
                    $m->isDecision() ? $m->pass_threshold : '', $m->votes_count, $this->safe($counts),
                    $m->opens_at?->toDateTimeString(), $m->closes_at?->toDateTimeString(), $m->closed_at?->toDateTimeString(),
                ]);
            }
            fclose($out);
        }, 'motions-'.now()->format('Ymd').'.csv', ['Content-Type' => 'text/csv']);
    }

    /** Neutralise spreadsheet formula injection. */
    private function safe(string $value): string
    {
        return preg_match('/^[=+\-@\t\r]/', $value) ? "'".$value : $value;
    }

    private function filtered(Request $request): Builder
    {
        return Motion::query()
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->toString()))
            ->when($request->filled('kind'), fn ($q) => $q->where('kind', $request->string('kind')->toString()))
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%'.str_replace(['%', '_'], ['\%', '\_'], $request->string('q')->toString()).'%'));
    }
}
