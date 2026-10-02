<x-account.shell title="Votes" active="votes">
    <div class="vt">
        <header class="vt-head">
            <div>
                <p class="vt-eyebrow">Member governance</p>
                <h1>Votes</h1>
                <p class="vt-lead">Decisions put to the membership. Each eligible member has one voice, and every vote is cast here in your dashboard.</p>
            </div>
            @if ($canCreate)
                <a href="{{ route('votes.create') }}" class="vt-btn vt-btn-solid"><x-icon name="plus" />New motion</a>
            @endif
        </header>

        @if (session('status'))
            <div class="vt-alert" role="status"><x-icon name="check-circle" /><span>{{ session('status') }}</span></div>
        @endif

        <p id="vt-live-region" class="vt-sr" aria-live="polite"></p>

        <section class="vt-section" aria-labelledby="vt-open">
            <h2 id="vt-open">Open for your vote <span class="vt-count">{{ $open->count() }}</span></h2>
            @if ($open->isEmpty())
                <div class="vt-empty">Nothing is open for your vote right now. You will be notified when a motion opens.</div>
            @else
                <div class="vt-grid">
                    @foreach ($open as $motion)
                        @include('votes._card', ['motion' => $motion])
                    @endforeach
                </div>
            @endif
        </section>

        @if ($mine->isNotEmpty())
            <section class="vt-section" aria-labelledby="vt-mine">
                <h2 id="vt-mine">Drafts and scheduled by me <span class="vt-count">{{ $mine->count() }}</span></h2>
                <div class="vt-grid">
                    @foreach ($mine as $motion)
                        @include('votes._card', ['motion' => $motion])
                    @endforeach
                </div>
            </section>
        @endif

        <section class="vt-section" aria-labelledby="vt-decided">
            <h2 id="vt-decided">Decided <span class="vt-count">{{ $decided->count() }}</span></h2>
            @if ($decided->isEmpty())
                <div class="vt-empty">No motion has been decided yet.</div>
            @else
                <div class="vt-grid">
                    @foreach ($decided as $motion)
                        @include('votes._card', ['motion' => $motion])
                    @endforeach
                </div>
            @endif
        </section>
    </div>
    @include('votes._scripts')
</x-account.shell>
