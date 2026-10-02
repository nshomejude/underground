@if (isset($score) && $score !== null)
    <span class="nw-match" style="--p: {{ $score }}" title="Match score {{ $score }} out of 100">
        <span class="nw-match-bar" aria-hidden="true"><i></i></span>
        <b>{{ $score }}%</b> match
    </span>
@endif
