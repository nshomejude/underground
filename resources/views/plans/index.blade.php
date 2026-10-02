<x-account.shell title="Membership plans" active="plans">
    <header class="ac-top">
        <div>
            <p class="ac-eyebrow">Member Area</p>
            <h1>Membership plans</h1>
            <p class="ac-sub">Compare the three tiers and ask to move up.</p>
        </div>
        @if ($isMember)
            <a href="{{ route('plans.requests') }}" class="ac-btn">My requests</a>
        @endif
    </header>

    @if (session('status'))
        <p class="ac-flash ac-flash-ok" role="status">{{ session('status') }}</p>
    @endif

    <section class="ac-panel pl-current" style="--i:0" aria-labelledby="pl-current-h">
        <p class="ac-eyebrow">Your plan</p>
        @if ($current)
            <h2 id="pl-current-h">{{ $current->name }}</h2>
            <p class="ac-lead">{{ $current->tagline }}</p>
            @if ($pending)
                <p class="ac-lead">You have asked to move to <strong>{{ $pending->toPlan->name }}</strong>. <a class="pl-link" href="{{ route('plans.requests') }}">See the request</a>.</p>
            @endif
        @elseif ($isMember)
            <h2 id="pl-current-h">Approved member</h2>
            <p class="ac-lead">Your membership is active. Plan details for your tier have not been published yet.</p>
        @else
            <h2 id="pl-current-h">No membership yet</h2>
            <p class="ac-lead">Membership is by application. Pick the plan that fits and apply; a partner reviews every application personally.</p>
        @endif
    </section>

    <div class="pl pl-in-account">
        @include('plans._compare', ['mode' => 'member'])
    </div>

    <p class="pl-fine pl-fine-page">Plans are agreed with our team. No payment is taken on this site.</p>
</x-account.shell>
