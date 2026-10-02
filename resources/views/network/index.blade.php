<x-account.shell title="Network" active="network">
    <header class="ac-top">
        <div>
            <p class="ac-eyebrow">Member Network</p>
            <h1>Directory</h1>
            <p class="ac-sub">Verified members of Underground, ready to do business with.</p>
        </div>
        <nav class="nw-top-links" aria-label="Network sections">
            <a class="ac-btn" href="{{ route('network.matches') }}"><x-icon name="sparkles" class="ac-bi" /> Recommended</a>
            <a class="ac-btn" href="{{ route('network.connections') }}"><x-icon name="users" class="ac-bi" /> My connections</a>
        </nav>
    </header>

    @if (session('status'))
        <p class="ac-flash ac-flash-ok" role="status">{{ session('status') }}</p>
    @endif

    @include('network.partials.status-prompt')

    @if ($top->isNotEmpty())
        <section class="nw-top3" aria-labelledby="nw-top3-h">
            <div class="nw-sec-head">
                <h2 id="nw-top3-h"><x-icon name="sparkles" /> Your best matches</h2>
                <a href="{{ route('network.matches') }}" class="ac-link">See all recommendations</a>
            </div>
            <ul class="nw-top3-list">
                @foreach ($top as $m)
                    @php($tp = $m['profile'])
                    <li>
                        <a href="{{ route('network.show', $tp->slug) }}" class="nw-mini">
                            @include('network.partials.avatar', ['profile' => $tp])
                            <span class="nw-mini-body">
                                <b>{{ $tp->display_name ?: $tp->user->name }}</b>
                                <small>{{ $m['reasons'][0] ?? $tp->headline }}</small>
                            </span>
                            <span class="nw-mini-score">{{ $m['score'] }}%</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    <form method="GET" action="{{ route('network.index') }}" class="nw-filters" id="nw-filters" role="search">
        <div class="nw-bar">
            <label class="nw-search">
                <span class="sr-only">Search members</span>
                <x-icon name="search" />
                <input type="search" name="q" value="{{ $filters['q'] }}" class="ac-input" placeholder="Search name, headline or organisation" maxlength="100">
            </label>
            <label class="nw-sort">
                <span class="sr-only">Sort by</span>
                <select name="sort" class="ac-input" onchange="this.form.submit()">
                    <option value="match" @selected($filters['sort'] === 'match')>Best match</option>
                    <option value="newest" @selected($filters['sort'] === 'newest')>Newest</option>
                    <option value="name" @selected($filters['sort'] === 'name')>Name A to Z</option>
                </select>
            </label>
            <button type="button" class="ac-btn nw-filter-toggle" data-nw-sheet aria-expanded="false" aria-controls="nw-panel">
                <x-icon name="sliders-horizontal" class="ac-bi" /> Filters
            </button>
            <button type="submit" class="ac-btn ac-btn-solid">Search</button>
        </div>

        <div class="nw-panel" id="nw-panel">
            <div class="nw-panel-head">
                <b>Filters</b>
                <button type="button" class="ac-iconbtn" data-nw-sheet-close aria-label="Close filters"><x-icon name="x" /></button>
            </div>
            <div class="nw-panel-body">
                <fieldset class="nw-fs nw-fs-wide">
                    <legend>Sectors</legend>
                    <div class="nw-checks">
                        @foreach (config('network.sectors') as $slug => $label)
                            <label class="nw-check"><input type="checkbox" name="sector[]" value="{{ $slug }}" @checked(in_array($slug, $filters['sector'], true))><span>{{ $label }}</span></label>
                        @endforeach
                    </div>
                </fieldset>
                <div class="nw-selects">
                    <label class="ac-field"><span class="ac-label">Supply-chain role</span>
                        <select name="role" class="ac-input">
                            <option value="">Any role</option>
                            @foreach (config('network.supply_chain_roles') as $slug => $label)
                                <option value="{{ $slug }}" @selected($filters['role'] === $slug)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="ac-field"><span class="ac-label">Membership tier</span>
                        <select name="tier" class="ac-input">
                            <option value="">Any tier</option>
                            @foreach (array_keys(config('network.tier_ranks')) as $slug)
                                <option value="{{ $slug }}" @selected($filters['tier'] === $slug)>{{ $tierNames[$slug] ?? \Illuminate\Support\Str::headline($slug) }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="ac-field"><span class="ac-label">Country</span>
                        <select name="country" class="ac-input">
                            <option value="">Anywhere</option>
                            @foreach ($countries as $c)
                                <option value="{{ $c }}" @selected($filters['country'] === $c)>{{ $c }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
                <div class="nw-toggles">
                    <label class="nw-check"><input type="checkbox" name="open" value="1" @checked($filters['open'])><span>Open to collaboration</span></label>
                    <label class="nw-check"><input type="checkbox" name="company" value="1" @checked($filters['company'])><span>Verified company only</span></label>
                </div>
            </div>
            <div class="nw-panel-foot">
                <a href="{{ route('network.index') }}" class="ac-btn">Clear</a>
                <button type="submit" class="ac-btn ac-btn-solid">Show results</button>
            </div>
        </div>
    </form>

    <div class="nw-count" aria-live="polite">
        <span><b>{{ $paginator->total() }}</b> {{ \Illuminate\Support\Str::plural('member', $paginator->total()) }}</span>
        @if ($active)
            <a href="{{ route('network.index') }}" class="ac-link">Clear filters</a>
        @endif
    </div>

    @if ($cards->isEmpty())
        <div class="ac-panel nw-empty">
            <x-icon name="users" />
            <h2>{{ $active ? 'No members match these filters' : 'No members to show yet' }}</h2>
            <p>
                @if ($active)
                    Try removing a filter or searching a broader term.
                @else
                    Members appear here once their identity is verified and their profile is visible. Check back soon.
                @endif
            </p>
            @if ($active)<a href="{{ route('network.index') }}" class="ac-btn">Clear filters</a>@endif
        </div>
    @else
        <div class="nw-grid">
            @foreach ($cards as $card)
                @include('network.partials.member-card', ['card' => $card, 'showReasons' => true])
            @endforeach
        </div>
        <div class="nw-pager">{{ $paginator->onEachSide(1)->links() }}</div>
    @endif

    <div class="nw-backdrop" data-nw-sheet-close hidden></div>
    @include('network.partials.sheet-script')
</x-account.shell>
