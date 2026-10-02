<x-account.shell title="Recommended" active="network">
    <header class="ac-top">
        <div>
            <p class="ac-eyebrow">Member Network</p>
            <h1>Recommended for you</h1>
            <p class="ac-sub">Members whose sectors, roles and goals fit yours, with the reasons why.</p>
        </div>
        <nav class="nw-top-links" aria-label="Network sections">
            <a class="ac-btn" href="{{ route('network.index') }}"><x-icon name="users" class="ac-bi" /> Directory</a>
        </nav>
    </header>

    @include('network.partials.status-prompt')

    @if ($cards->isEmpty())
        <div class="ac-panel nw-empty">
            <x-icon name="sparkles" />
            @if ($viewer['profile'] === null || empty($viewer['profile']->sectors))
                <h2>Tell us what you do to get matches</h2>
                <p>Add your sectors, supply-chain role and what you are looking for, and we will recommend members who fit.</p>
                @if (\Illuminate\Support\Facades\Route::has('account.profile'))
                    <a href="{{ route('account.profile') }}" class="ac-btn ac-btn-solid">Complete my profile</a>
                @endif
            @else
                <h2>No matches yet</h2>
                <p>As more members join and verify, recommendations will appear here. You can also browse the full directory.</p>
                <a href="{{ route('network.index') }}" class="ac-btn">Browse the directory</a>
            @endif
        </div>
    @else
        <div class="nw-grid">
            @foreach ($cards as $card)
                @include('network.partials.member-card', ['card' => $card, 'showReasons' => true])
            @endforeach
        </div>
    @endif
</x-account.shell>
