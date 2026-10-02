@php
    $labels = ['passed' => 'PASSED', 'rejected' => 'REJECTED', 'no_quorum' => 'NO QUORUM', 'decided' => 'DECIDED', 'tied' => 'TIED'];
    $outcome = $tally['outcome'] ?? 'no_quorum';
    $winners = $tally['winners'] ?? [];
    $statement = $tally['statement'] ?? \App\Services\MotionService::statement($motion, $tally);
    $t = rtrim(rtrim(number_format((float) $tally['threshold'], 2, '.', ''), '0'), '.');
@endphp

<section class="vt-panel vt-result" aria-labelledby="vt-result-h">
    @if ($final)
        <x-seal :size="72" />
        <h2 id="vt-result-h" class="vt-sr">Result</h2>
        <p class="vt-outcome" aria-live="polite">{{ $labels[$outcome] ?? 'CLOSED' }}</p>
        <p class="vt-outcome-icon"><x-icon name="shield-check" />Recorded {{ \Illuminate\Support\Carbon::parse($tally['closed_at'] ?? $motion->closed_at)->format('j F Y, H:i') }}</p>
        <p class="vt-decision">{{ $statement }}</p>
    @else
        <h2 id="vt-result-h">Live results</h2>
        <p class="vt-outcome-icon"><x-icon name="eye" />Provisional: voting is still open</p>
    @endif

    <ul class="vt-tally" aria-label="Votes by choice">
        @foreach ($tally['counts'] as $choice => $count)
            <li class="{{ $final && in_array($choice, $winners, true) ? 'is-winner' : '' }}">
                <div class="vt-tally-top">
                    <span>{{ $choice }}{{ $final && in_array($choice, $winners, true) ? ' (leading)' : '' }}</span>
                    <span>{{ $count }} &middot; {{ rtrim(rtrim(number_format($tally['percentages'][$choice] ?? 0, 1), '0'), '.') }}%</span>
                </div>
                <div class="vt-tally-bar" aria-hidden="true"><span style="width: {{ $tally['percentages'][$choice] ?? 0 }}%"></span></div>
            </li>
        @endforeach
    </ul>

    <dl class="vt-facts">
        <div><dt>Members voting</dt><dd>{{ $tally['voters'] }}</dd></div>
        <div><dt>Quorum</dt><dd>{{ $tally['quorum'] }} {{ $tally['quorum_met'] ? '(met)' : '(not met)' }}</dd></div>
        @if ($motion->isDecision())
            <div><dt>Threshold</dt><dd>{{ $t }}% of For + Against</dd></div>
            <div><dt>For share</dt><dd>{{ $tally['for_share'] === null ? 'n/a' : $tally['for_share'].'%' }}</dd></div>
        @endif
        @if ($tally['weighted'])
            <div><dt>Weighting</dt><dd>By tier (total {{ $tally['weighted_total'] }})</dd></div>
        @endif
    </dl>
</section>
