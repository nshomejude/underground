@php
    $name = $profile->display_name ?: $profile->user->name;
    $sectorNames = config('network.sectors');
    $roleNames = config('network.supply_chain_roles');
    $kindNames = config('network.seeking_kinds');
    $place = collect([$profile->city, $profile->country])->filter()->implode(', ');
    $sectors = collect((array) $profile->sectors)->map(fn ($s) => ['slug' => $s, 'label' => $sectorNames[$s] ?? null])->filter(fn ($s) => $s['label']);
    $roles = collect((array) $profile->supply_chain_roles)->map(fn ($r) => $roleNames[$r] ?? null)->filter();
    $seeking = collect((array) $profile->seeking_kinds)->map(fn ($k) => $kindNames[$k] ?? null)->filter();
    $services = collect((array) $profile->services)->filter();
    $portfolio = collect((array) $profile->portfolio)->filter();
    $languages = collect((array) $profile->languages)->filter();
    $state = $relation['state'];
    $connection = $relation['connection'];
    $has = fn (string $r) => \Illuminate\Support\Facades\Route::has($r);
    $item = fn ($v, string ...$keys) => is_array($v) ? (collect($keys)->map(fn ($k) => $v[$k] ?? null)->first(fn ($x) => filled($x))) : null;
@endphp
<x-account.shell :title="$name" active="network">
    <a href="{{ route('network.index') }}" class="ac-link ac-back"><x-icon name="arrow-left" class="ac-bi" /> Directory</a>

    @if (session('status'))
        <p class="ac-flash ac-flash-ok" role="status">{{ session('status') }}</p>
    @endif
    @if (session('network_error'))
        <p class="ac-flash ac-flash-warn" role="alert">{{ session('network_error') }}</p>
    @endif
    @if ($isSelf)
        <p class="ac-flash ac-flash-ok" role="status"><x-icon name="eye" /> This is how others see you.
            @if ($has('account.profile'))<a href="{{ route('account.profile') }}" class="ac-link">Edit your profile</a>@endif
        </p>
    @endif

    <section class="nw-hero">
        @include('network.partials.avatar', ['profile' => $profile, 'size' => 'nw-avatar--xl'])
        <div class="nw-hero-body">
            <div class="nw-chips">
                @include('network.partials.tier-chip', ['tier' => $tier])
                @include('network.partials.badges', ['profile' => $profile, 'companyVerified' => $companyVerified])
                @if ($profile->open_to_collaboration)
                    <span class="nw-badge nw-badge--open"><x-icon name="handshake" /> Open to collaboration</span>
                @endif
            </div>
            <h1>{{ $name }}</h1>
            @if ($profile->headline)<p class="nw-hero-headline">{{ $profile->headline }}</p>@endif
            <p class="nw-meta">
                @if ($place !== '')<span><x-icon name="map-pin" /> {{ $place }}</span>@endif
                @if ($languages->isNotEmpty())<span><x-icon name="globe" /> {{ $languages->implode(', ') }}</span>@endif
                @if ($profile->organisation_name)<span><x-icon name="building-2" /> {{ $profile->organisation_name }}</span>@endif
            </p>
        </div>

        @unless ($isSelf)
            <div class="nw-actions">
                @if ($state === 'none')
                    @if ($canConnect)
                        <button type="button" class="ac-btn ac-btn-solid" data-nw-open="connect"><x-icon name="user-plus" class="ac-bi" /> Connect</button>
                        <button type="button" class="ac-btn" data-nw-open="collaborate"><x-icon name="handshake" class="ac-bi" /> Request collaboration</button>
                    @else
                        <p class="nw-note">Verify your identity to connect with members.
                            @if ($has('verification.index'))<a href="{{ route('verification.index') }}" class="ac-link">Start verification</a>@endif
                        </p>
                    @endif
                @elseif ($state === 'sent')
                    <span class="nw-state"><x-icon name="clock" /> {{ $connection->isCollaboration() ? 'Collaboration' : 'Connection' }} request sent</span>
                    <form method="POST" action="{{ route('network.withdraw', $connection) }}">@csrf
                        <button class="ac-btn" type="submit"><x-icon name="undo-2" class="ac-bi" /> Withdraw</button>
                    </form>
                @elseif ($state === 'received')
                    <span class="nw-state"><x-icon name="user-plus" /> {{ $name }} wants to {{ $connection->isCollaboration() ? 'collaborate' : 'connect' }}</span>
                    <form method="POST" action="{{ route('network.respond', $connection) }}">@csrf
                        <input type="hidden" name="action" value="accept">
                        <button class="ac-btn ac-btn-solid" type="submit"><x-icon name="check" class="ac-bi" /> Accept</button>
                    </form>
                    <form method="POST" action="{{ route('network.respond', $connection) }}">@csrf
                        <input type="hidden" name="action" value="decline">
                        <button class="ac-btn" type="submit">Decline</button>
                    </form>
                @elseif ($state === 'connected')
                    <span class="nw-state nw-state--ok"><x-icon name="check-circle" /> Connected</span>
                    @if ($conversation && $has('messages.show'))
                        <a class="ac-btn ac-btn-solid" href="{{ route('messages.show', $conversation) }}"><x-icon name="message-square" class="ac-bi" /> Message</a>
                    @elseif ($has('messages.index'))
                        <a class="ac-btn ac-btn-solid" href="{{ route('messages.index') }}"><x-icon name="message-square" class="ac-bi" /> Messages</a>
                    @endif
                @elseif ($state === 'blocked')
                    <span class="nw-state"><x-icon name="ban" /> You blocked this member</span>
                    <form method="POST" action="{{ route('network.unblock', $profile->slug) }}">@csrf
                        <button class="ac-btn" type="submit">Unblock</button>
                    </form>
                @elseif ($state === 'cooldown')
                    <p class="nw-note">This member declined your last request. You can try again in a few weeks.</p>
                @endif

                @if (! in_array($state, ['blocked'], true))
                    <form method="POST" action="{{ route('network.block', $profile->slug) }}" onsubmit="return confirm('Block {{ addslashes($name) }}? They will no longer see you or be able to contact you.');">@csrf
                        <button class="nw-linkbtn" type="submit"><x-icon name="ban" /> Block</button>
                    </form>
                @endif
            </div>
        @endunless
    </section>

    @if (! $isSelf && ($match['score'] ?? null) !== null && $match['reasons'] !== [])
        <section class="ac-panel nw-why" aria-labelledby="nw-why-h">
            <div class="nw-sec-head">
                <h2 id="nw-why-h"><x-icon name="sparkles" /> Why you may fit</h2>
                @include('network.partials.match', ['score' => $match['score']])
            </div>
            <ul class="nw-reasons nw-reasons--full">
                @foreach ($match['reasons'] as $reason)<li><x-icon name="check" /> {{ $reason }}</li>@endforeach
            </ul>
        </section>
    @endif

    <div class="nw-cols">
        <div class="nw-main">
            @if ($profile->bio)
                <section class="ac-panel"><h2>About</h2><p class="nw-prose">{{ $profile->bio }}</p></section>
            @endif

            <section class="ac-panel">
                <h2>Sectors &amp; supply-chain role</h2>
                @if ($sectors->isNotEmpty())
                    <ul class="nw-tags nw-tags--lg" aria-label="Sectors">
                        @foreach ($sectors as $s)
                            <li @class(['is-shared' => in_array($s['slug'], $shared, true)])>{{ $s['label'] }}</li>
                        @endforeach
                    </ul>
                @endif
                @if ($roles->isNotEmpty())
                    <p class="nw-roles"><span>Supply-chain role</span> {{ $roles->implode(' · ') }}</p>
                @endif
                @if ($sectors->isEmpty() && $roles->isEmpty())<p class="nw-muted">Not shared yet.</p>@endif
            </section>

            <div class="nw-two">
                <section class="ac-panel">
                    <h2>What they offer</h2>
                    @if ($profile->offering_summary)<p class="nw-prose">{{ $profile->offering_summary }}</p>@endif
                    @if ($services->isNotEmpty())
                        <ul class="nw-list">
                            @foreach ($services as $svc)
                                <li><x-icon name="check" />
                                    <span>
                                        <b>{{ is_array($svc) ? $item($svc, 'title', 'name', 'label') : $svc }}</b>
                                        @if (is_array($svc) && $item($svc, 'description', 'summary'))<br>{{ $item($svc, 'description', 'summary') }}@endif
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @elseif (! $profile->offering_summary)
                        <p class="nw-muted">Not shared yet.</p>
                    @endif
                </section>
                <section class="ac-panel">
                    <h2>What they seek</h2>
                    @if ($seeking->isNotEmpty())
                        <ul class="nw-tags" aria-label="Looking for">
                            @foreach ($seeking as $k)<li>{{ $k }}</li>@endforeach
                        </ul>
                    @endif
                    @if ($profile->seeking_summary)<p class="nw-prose">{{ $profile->seeking_summary }}</p>@endif
                    @if ($seeking->isEmpty() && ! $profile->seeking_summary)<p class="nw-muted">Not shared yet.</p>@endif
                </section>
            </div>

            @if ($portfolio->isNotEmpty())
                <section class="ac-panel">
                    <h2>Portfolio</h2>
                    <div class="nw-portfolio">
                        @foreach ($portfolio as $work)
                            @php($url = is_array($work) ? $item($work, 'url', 'link') : null)
                            <article class="nw-work">
                                <h3>{{ is_array($work) ? $item($work, 'title', 'name') : $work }}</h3>
                                @if (is_array($work) && $item($work, 'description', 'summary'))<p>{{ $item($work, 'description', 'summary') }}</p>@endif
                                @if (is_array($work) && $item($work, 'year', 'sector'))<small>{{ $item($work, 'year', 'sector') }}</small>@endif
                                @if ($url && preg_match('#^https?://#i', $url))
                                    <a href="{{ $url }}" class="ac-link" rel="noopener noreferrer nofollow" target="_blank">View project</a>
                                @endif
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>

        <aside class="nw-side">
            @if ($profile->organisation_name || $profile->organisation_role || $profile->organisation_size)
                <section class="ac-panel">
                    <h2>Organisation</h2>
                    <dl class="nw-dl">
                        @if ($profile->organisation_name)<div><dt>Name</dt><dd>{{ $profile->organisation_name }}</dd></div>@endif
                        @if ($profile->organisation_role)<div><dt>Role</dt><dd>{{ $profile->organisation_role }}</dd></div>@endif
                        @if ($profile->organisation_size)<div><dt>Size</dt><dd>{{ $profile->organisation_size }}</dd></div>@endif
                        @if ($profile->organisation_website && preg_match('#^https?://#i', $profile->organisation_website))
                            <div><dt>Website</dt><dd><a class="ac-link" href="{{ $profile->organisation_website }}" rel="noopener noreferrer nofollow" target="_blank">{{ parse_url($profile->organisation_website, PHP_URL_HOST) }}</a></dd></div>
                        @endif
                    </dl>
                </section>
            @endif

            <section class="ac-panel">
                <h2>Connections <span class="nw-count-pill">{{ $connectionCount }}</span></h2>
                @if (! $isSelf && count($mutualIds) > 0)
                    <p class="nw-mutual"><x-icon name="users" /> {{ count($mutualIds) }} mutual {{ \Illuminate\Support\Str::plural('connection', count($mutualIds)) }}</p>
                @endif
                @if ($connections->isEmpty())
                    <p class="nw-muted">No visible connections yet.</p>
                @else
                    <ul class="nw-avatars">
                        @foreach ($connections->sortByDesc(fn ($c) => in_array($c->user_id, $mutualIds, true)) as $c)
                            <li>
                                <a href="{{ route('network.show', $c->slug) }}" @class(['nw-chip-link', 'is-mutual' => in_array($c->user_id, $mutualIds, true)])>
                                    @include('network.partials.avatar', ['profile' => $c, 'size' => 'nw-avatar--sm'])
                                    <span>{{ $c->display_name ?: $c->user->name }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </aside>
    </div>

    @if (! $isSelf && $state === 'none' && $canConnect)
        <dialog class="nw-dialog" id="nw-dialog" aria-labelledby="nw-dialog-h" @if ($errors->any() && old('kind')) data-open-on-load data-kind="{{ old('kind') }}" @endif>
            <form method="POST" action="{{ route('network.connect', $profile->slug) }}" class="nw-dialog-form">
                @csrf
                <input type="hidden" name="kind" id="nw-kind" value="{{ old('kind', 'connect') }}">
                <header>
                    <h2 id="nw-dialog-h" data-title>Connect with {{ $name }}</h2>
                    <button type="button" class="ac-iconbtn" data-nw-close aria-label="Close"><x-icon name="x" /></button>
                </header>

                <div class="ac-field" data-collab hidden>
                    <label class="ac-label" for="nw-topic">Topic</label>
                    <input class="ac-input" id="nw-topic" name="topic" maxlength="120" value="{{ old('topic') }}" placeholder="e.g. Joint bid for a port expansion">
                    @error('topic')<p class="ac-err">{{ $message }}</p>@enderror
                </div>

                @if (count($shared) > 0)
                    <fieldset class="nw-fs" data-collab hidden>
                        <legend>Sectors in common</legend>
                        <div class="nw-checks">
                            @foreach ($shared as $slug)
                                <label class="nw-check"><input type="checkbox" name="sectors[]" value="{{ $slug }}" @checked(in_array($slug, old('sectors', $shared), true))><span>{{ $sectorNames[$slug] ?? $slug }}</span></label>
                            @endforeach
                        </div>
                    </fieldset>
                @endif

                <div class="ac-field">
                    <label class="ac-label" for="nw-message">Your message <span class="ac-hint">20 to 600 characters</span></label>
                    <textarea class="ac-input" id="nw-message" name="message" rows="5" minlength="20" maxlength="600" required placeholder="Introduce yourself and say why you would like to connect.">{{ old('message') }}</textarea>
                    @error('message')<p class="ac-err">{{ $message }}</p>@enderror
                </div>

                <footer>
                    <button type="button" class="ac-btn" data-nw-close>Cancel</button>
                    <button type="submit" class="ac-btn ac-btn-solid"><x-icon name="send" class="ac-bi" /> Send request</button>
                </footer>
            </form>
        </dialog>
        <script>
            (function () {
                var dlg = document.getElementById('nw-dialog');
                if (!dlg) return;
                var kind = document.getElementById('nw-kind');
                var title = dlg.querySelector('[data-title]');
                function show(k) {
                    kind.value = k;
                    dlg.querySelectorAll('[data-collab]').forEach(function (el) { el.hidden = k !== 'collaborate'; });
                    var t = dlg.querySelector('#nw-topic'); if (t) t.required = k === 'collaborate';
                    title.textContent = (k === 'collaborate' ? 'Request collaboration with ' : 'Connect with ') + @json($name);
                    if (typeof dlg.showModal === 'function') { if (!dlg.open) dlg.showModal(); } else { dlg.setAttribute('open', ''); }
                }
                document.querySelectorAll('[data-nw-open]').forEach(function (b) { b.addEventListener('click', function () { show(b.getAttribute('data-nw-open')); }); });
                dlg.querySelectorAll('[data-nw-close]').forEach(function (b) { b.addEventListener('click', function () { dlg.close(); }); });
                dlg.addEventListener('click', function (e) { if (e.target === dlg) dlg.close(); });
                if (dlg.hasAttribute('data-open-on-load')) show(dlg.getAttribute('data-kind'));
            })();
        </script>
    @endif
</x-account.shell>
