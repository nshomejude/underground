@php
    $labels = ['passed' => 'Passed', 'rejected' => 'Rejected', 'no_quorum' => 'No quorum', 'decided' => 'Decided', 'tied' => 'Tied'];
    $mine = $myVotes[$motion->id] ?? null;
    $open = $motion->isOpenNow();
    $quorum = max(1, (int) $motion->quorum);
    $pct = min(100, (int) round($motion->votes_count / $quorum * 100));
    $met = $motion->votes_count >= $quorum;
@endphp

<article class="vt-card">
    <div class="vt-chips">
        <span class="vt-chip vt-chip-gold">{{ $motion->kindLabel() }}</span>
        <span class="vt-chip"><x-icon name="users" />{{ $motion->tierLabel() }}</span>
        @if ($motion->anonymous)
            <span class="vt-chip"><x-icon name="eye" />Anonymous</span>
        @endif
    </div>

    <h3><a href="{{ route('votes.show', $motion) }}">{{ $motion->title }}</a></h3>

    @if ($motion->summary)
        <p>{{ \Illuminate\Support\Str::limit($motion->summary, 160) }}</p>
    @endif

    @if ($motion->isDraft())
        <span class="vt-status"><x-icon name="file-text" />Draft, not yet published</span>
    @elseif ($motion->isScheduled())
        <span class="vt-time"><x-icon name="clock" />Opens {{ $motion->opens_at->format('j M Y, H:i') }}</span>
    @elseif ($open)
        <span class="vt-time">
            <x-icon name="clock" />
            <time datetime="{{ $motion->closes_at->toIso8601String() }}" data-vt-countdown="{{ $motion->closes_at->toIso8601String() }}">{{ $motion->closes_at->diffForHumans(['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) }} left</time>
        </span>
        @if ($mine !== null)
            <span class="vt-status vt-status-ok"><x-icon name="check-circle" />Voted: {{ $mine }}</span>
        @else
            <span class="vt-status vt-status-todo"><x-icon name="triangle-alert" />Not voted yet</span>
        @endif
    @elseif ($motion->isCancelled())
        <span class="vt-status"><x-icon name="ban" />Withdrawn</span>
    @else
        <span class="vt-status"><x-icon name="shield-check" />{{ strtoupper($labels[$motion->outcome] ?? 'Closed') }}</span>
        <span class="vt-time"><x-icon name="clock" />Closed {{ $motion->closed_at?->format('j M Y') }}</span>
        <span class="vt-status {{ $mine !== null ? 'vt-status-ok' : '' }}">
            <x-icon :name="$mine !== null ? 'check-circle' : 'circle-x'" />{{ $mine !== null ? 'You voted: '.$mine : 'You did not vote' }}
        </span>
    @endif

    @if (! $motion->isDraft() && ! $motion->isCancelled())
        <div class="vt-meter">
            <div class="vt-meter-bar" role="img" aria-label="{{ $motion->votes_count }} of {{ $quorum }} members needed for quorum">
                <div class="vt-meter-fill {{ $met ? 'is-met' : '' }}" style="width: {{ $pct }}%"></div>
            </div>
            <div class="vt-meter-text">
                <span>{{ $motion->votes_count }} {{ \Illuminate\Support\Str::plural('voter', $motion->votes_count) }}</span>
                <span>{{ $met ? 'Quorum met' : 'Quorum: '.$quorum }}</span>
            </div>
        </div>
    @endif
</article>
