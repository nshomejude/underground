@php
    $active = $row && $row->isActive();
    $routeShow = 'verification.'.$kind.'.show';
    $until = $status === 'approved' && $row ? $row->expires_at?->format('j M Y') : null;
    if ($status === 'approved' && $row && ! $row->isApproved()) {
        $until = null;
    }
@endphp
<section class="ac-panel vf-card" aria-labelledby="vf-{{ $kind }}">
    <div class="vf-card-head">
        <span class="vf-ico"><x-icon :name="$icon" /></span>
        <div>
            <h2 id="vf-{{ $kind }}">{{ $title }}</h2>
            <div style="margin-top:6px"><x-verification.chip :status="$status" :until="$until" /></div>
        </div>
    </div>
    <p>{{ $blurb }}</p>
    <ul class="vf-unlocks" aria-label="What this unlocks">
        @foreach ($unlocks as $u)<li><x-icon name="check" />{{ $u }}</li>@endforeach
    </ul>
    @if ($row && $row->status === 'rejected' && $status === 'rejected')
        <div class="vf-note vf-note--bad"><x-icon name="circle-x" /><div><strong>Not approved</strong>{{ $row->rejection_reason }}</div></div>
    @endif
    @if ($row && $row->status === 'in_review' && $row->info_request)
        <div class="vf-note vf-note--warn"><x-icon name="message-square" /><div><strong>Our reviewer asked</strong>{{ $row->info_request }}</div></div>
    @endif
    <div class="vf-next">
        @if ($active)
            <a class="ac-btn ac-btn-solid" href="{{ route($routeShow) }}">
                {{ $row->status === 'draft' ? 'Continue' : 'View status' }} <x-icon name="arrow-right" class="h-4 w-4" />
            </a>
        @elseif ($canStart)
            <form method="POST" action="{{ route('verification.'.$kind.'.start') }}">@csrf
                <button type="submit" class="ac-btn ac-btn-solid">
                    {{ in_array($status, ['rejected', 'expired']) ? 'Try again' : ($status === 'approved' ? 'Renew' : 'Start verification') }}
                    <x-icon name="arrow-right" class="h-4 w-4" />
                </button>
            </form>
        @endif
        @if ($row && ! $active && $row->status !== 'withdrawn')
            <a class="ac-link" href="{{ route($routeShow) }}">View details</a>
        @endif
    </div>
</section>
