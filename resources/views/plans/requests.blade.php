<x-account.shell title="My plan requests" active="plans">
    <header class="ac-top">
        <div>
            <p class="ac-eyebrow">Member Area</p>
            <h1>My plan requests</h1>
            <p class="ac-sub">Requests to move to another plan, and where each one stands.</p>
        </div>
        <a href="{{ route('plans.index') }}" class="ac-btn">Membership plans</a>
    </header>

    @if (session('status'))
        <p class="ac-flash ac-flash-ok" role="status">{{ session('status') }}</p>
    @endif

    <div class="ac-stack pl-requests">
        @forelse ($requests as $req)
            <section class="ac-panel" style="--i:0" aria-labelledby="req-{{ $req->id }}">
                <p class="ac-eyebrow">{{ $req->created_at->format('j M Y') }}</p>
                <h2 id="req-{{ $req->id }}">
                    {{ $req->fromPlan?->name ?? 'Membership' }} <span aria-hidden="true">&rarr;</span><span class="sr-only">to</span> {{ $req->toPlan->name }}
                </h2>
                <p><span class="pl-status pl-status-{{ $req->status }}">{{ ucfirst($req->status) }}</span></p>
                @if ($req->note)
                    <p class="ac-lead">Your note: {{ $req->note }}</p>
                @endif
                @if ($req->response_note)
                    <p class="ac-lead">Our reply: {{ $req->response_note }}</p>
                @endif
                @if ($req->isPending())
                    <form method="POST" action="{{ route('plans.requests.withdraw', $req) }}" onsubmit="return confirm('Withdraw this request?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="ac-btn">Withdraw request</button>
                    </form>
                @endif
            </section>
        @empty
            <section class="ac-panel" style="--i:0">
                <h2>No requests yet</h2>
                <p class="ac-lead">When you ask to move to another plan, it will appear here.</p>
                <a href="{{ route('plans.index') }}" class="ac-btn ac-btn-solid">Compare plans</a>
            </section>
        @endforelse
    </div>
</x-account.shell>
