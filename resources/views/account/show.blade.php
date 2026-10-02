@php
    $statusTones = [
        'submitted' => 'info',
        'under_review' => 'warning',
    ];

    $user = auth()->user();
    $verified = $user->hasVerifiedEmail();
    $hasCertificateRoute = \Illuminate\Support\Facades\Route::has('account.certificate');

    $steps = [
        ['done' => true, 'label' => 'Create your account', 'href' => null],
        ['done' => $verified, 'label' => 'Verify your email address', 'href' => route('verification.notice')],
        ['done' => $state !== 'none', 'label' => 'Apply for a membership tier', 'href' => route('membership.index')],
        ['done' => $state === 'approved', 'label' => 'Receive your membership card', 'href' => $state === 'pending' ? route('membership.track') : null],
    ];
    $remaining = collect($steps)->where('done', false)->count();
    $percent = (int) round((count($steps) - $remaining) / count($steps) * 100);
    $benefits = [
        ['key', 'Private introductions', 'Vetted, one-to-one, handled by your liaison.'],
        ['globe', 'Global network', 'Access to partner convenings and delegations.'],
        ['eye', 'Confidential desk', 'A discreet channel for sensitive matters.'],
        ['star', 'Verified credential', 'Shareable proof of standing for counterparties.'],
    ];
@endphp

<x-account.shell title="My Account" active="overview">
    <header class="ac-top">
        <div>
            <p class="ac-eyebrow">Member Area {{ $state === "approved" ? "· ".$tier->name : "" }}</p>
            <h1>
                @if ($state === 'approved')
                    Your Membership Card
                @elseif ($state === 'pending')
                    Application Under Review
                @else
                    My Account
                @endif
            </h1>
            <p class="ac-sub">Signed in as {{ $user->name }}</p>
        </div>
        @if ($verified)
            <span class="ac-badge"><svg aria-hidden="true"><use href="#ai-chk"/></svg>Email verified</span>
        @endif
    </header>

    @if (session('status'))
        <p class="ac-flash ac-flash-ok" role="status">
            <svg aria-hidden="true"><use href="#ai-chk"/></svg>
            <span>{{ session('status') === 'verification-link-sent' ? 'A new verification link has been sent to '.$user->email.'.' : session('status') }}</span>
        </p>
    @endif

    @unless ($verified)
        <div class="ac-flash ac-flash-warn" role="alert">
            <p>
                <strong>Please verify your email.</strong>
                We sent a link to {{ $user->email }}. Check your spam folder if you cannot find it.
            </p>
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="ac-btn">Resend link</button>
            </form>
        </div>
    @endunless

    <div class="ac-grid">
        @if ($state === 'approved')
            <section class="ac-panel ac-wm ac-span" style="--i:0" id="card" aria-labelledby="h-card">
                <p class="ac-eyebrow">Credential</p>
                <h2 id="h-card">Membership credential</h2>
                <p class="ac-lead">Your permanent Underground membership card &mdash; carried, never advertised. Select "View Back" to see its verification face.</p>
                <div class="ac-card-box">
                    <x-membership-card
                        :variant="$variant"
                        :name="$name"
                        :representative="$representative"
                        :representative-title="$representativeTitle"
                        :tier="$tier"
                        :member-id="$memberId"
                        :issued-on="$issuedOn"
                        :valid-through="$validThrough"
                        :verify-url="$verifyUrl ?? null"
                        :serial="$serial ?? null"
                    />
                </div>
                <p class="ac-note">Application reference {{ $application->reference->value }} &middot; approved {{ $issuedOn->format('j F Y') }}.</p>
            </section>
        @elseif ($state === 'pending')
            <section class="ac-panel ac-span" style="--i:0" id="card" aria-labelledby="h-card">
                <p class="ac-eyebrow">Application</p>
                <h2 id="h-card">Still with the review committee</h2>
                <p class="ac-lead">
                    Your application is {{ strtolower($application->status()->label()) }}. Every application is
                    reviewed by a partner before a tier is granted &mdash; keep the reference below for your
                    records, and this page will reflect your membership card the moment it clears review.
                </p>
                <div class="ac-chips">
                    <x-status-badge :label="$application->status()->label()" :tone="$statusTones[$application->status()->value] ?? 'neutral'" />
                    <span class="ac-ref">{{ $application->reference->value }}</span>
                </div>
                <a href="{{ route('membership.track') }}" class="ac-btn ac-btn-solid ac-fit">Track application <svg aria-hidden="true"><use href="#ai-chk"/></svg></a>
            </section>
        @else
            <section class="ac-panel ac-span" style="--i:0" id="card" aria-labelledby="h-card">
                <p class="ac-eyebrow">Membership</p>
                <h2 id="h-card">You're not yet a member</h2>
                <p class="ac-lead">
                    Underground extends three vetted tiers to governments, principals, and corporate
                    institutions. There is no public checkout &mdash; every application is reviewed by a
                    partner before a tier is granted. Once approved, your permanent membership card will
                    appear here.
                </p>
                <a href="{{ route('membership.index') }}" class="ac-btn ac-btn-solid ac-fit">Explore Membership <svg aria-hidden="true"><use href="#ai-plus"/></svg></a>
            </section>
        @endif

        @if ($remaining > 0)
            <section class="ac-panel" style="--i:1" aria-labelledby="h-start">
                <p class="ac-eyebrow">Standing</p>
                <h2 id="h-start">Getting started</h2>
                <div class="ac-ringwrap">
                    <div class="ac-ring" role="img" aria-label="{{ count($steps) - $remaining }} of {{ count($steps) }} steps complete">
                        <svg width="120" height="120" viewBox="0 0 120 120" aria-hidden="true">
                            <circle class="tr" cx="60" cy="60" r="50" pathLength="100"/>
                            <circle class="pr" cx="60" cy="60" r="50" pathLength="100" style="--off: {{ 100 - $percent }}"/>
                        </svg>
                        <b data-count="{{ $percent }}" data-suffix="%">{{ $percent }}%</b>
                    </div>
                    <p>{{ count($steps) - $remaining }} of {{ count($steps) }} complete. Finish the steps below to unlock your full member area.</p>
                </div>
                <ol class="ac-steps">
                    @foreach ($steps as $step)
                        <li>
                            @if ($step['done'])
                                <svg class="ac-ok" aria-hidden="true"><use href="#ai-chk"/></svg>
                                <span class="ac-done">{{ $step['label'] }}</span>
                            @else
                                <span class="ac-num">{{ $loop->iteration }}</span>
                                @if ($step['href'])
                                    <a href="{{ $step['href'] }}">{{ $step['label'] }}</a>
                                @else
                                    <span>{{ $step['label'] }}</span>
                                @endif
                            @endif
                        </li>
                    @endforeach
                </ol>
            </section>
        @endif

        @isset($timeline)
            <section class="ac-panel ac-span" style="--i:2" aria-labelledby="h-tl">
                <p class="ac-eyebrow">Membership status</p>
                <h2 id="h-tl">Your journey</h2>
                <ol class="ac-tl">
                    @foreach ($timeline as $step)
                        <li class="{{ $step['state'] }}" @if ($step['state'] === 'now') aria-current="step" @endif>
                            <b>{{ $step['label'] }}</b>
                            <small>{{ $step['detail'] }}</small>
                        </li>
                    @endforeach
                </ol>
            </section>
        @endisset

        @if ($state === 'approved')
            <section class="ac-panel" style="--i:3" aria-labelledby="h-ben">
                <p class="ac-eyebrow">{{ $tier->name }}</p>
                <h2 id="h-ben">Your benefits</h2>
                <ul class="ac-benefits">
                    @foreach ($benefits as [$icon, $label, $copy])
                        <li>
                            <svg aria-hidden="true"><use href="#ai-{{ $icon }}"/></svg>
                            <div><b>{{ $label }}</b><small>{{ $copy }}</small></div>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        <section class="ac-panel" style="--i:4" aria-labelledby="h-qa">
            <p class="ac-eyebrow">Quick actions</p>
            <h2 id="h-qa">Do more, quietly</h2>
            <div class="ac-qa">
                @if ($state === 'approved')
                    @if ($hasCertificateRoute)
                        <a href="{{ route('account.certificate') }}" class="ac-btn ac-btn-solid"><svg aria-hidden="true"><use href="#ai-dl"/></svg>Download certificate</a>
                    @endif
                    @if (! empty($verifyUrl))
                        <button type="button" class="ac-btn" data-copy="{{ $verifyUrl }}" data-toast="Verification link copied"><svg aria-hidden="true"><use href="#ai-share"/></svg>Share verification link</button>
                    @endif
                @elseif ($state === 'pending')
                    <a href="{{ route('membership.track') }}" class="ac-btn ac-btn-solid"><svg aria-hidden="true"><use href="#ai-chk"/></svg>Track application</a>
                @endif
                <a href="{{ route('inquiries.create') }}" class="ac-btn @if ($state === 'none') ac-btn-solid @endif"><svg aria-hidden="true"><use href="#ai-plus"/></svg>Start confidential inquiry</a>
                <a href="{{ route('inquiries.track') }}" class="ac-btn"><svg aria-hidden="true"><use href="#ai-msg"/></svg>Track an inquiry</a>
            </div>
        </section>

        @if (($notifications ?? collect())->isNotEmpty())
            <section class="ac-panel" style="--i:5" aria-labelledby="h-act">
                <p class="ac-eyebrow">Notifications</p>
                <h2 id="h-act">Recent activity</h2>
                <ul class="ac-list">
                    @foreach ($notifications as $n)
                        <li>
                            <span class="ac-dot" aria-hidden="true"></span>
                            <div>
                                <b>{{ $n->data['title'] ?? $n->data['subject'] ?? \Illuminate\Support\Str::headline(class_basename($n->type)) }}</b>
                                @if (! empty($n->data['message'] ?? $n->data['body'] ?? null))
                                    <small>{{ $n->data['message'] ?? $n->data['body'] }}</small>
                                @endif
                            </div>
                            <time class="ac-time" datetime="{{ $n->created_at->toIso8601String() }}">{{ $n->created_at->diffForHumans() }}</time>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        <section class="ac-panel" style="--i:6" id="security" aria-labelledby="h-sec">
            <p class="ac-eyebrow">Protection</p>
            <h2 id="h-sec">Account security</h2>
            <div class="ac-sec">
                <div class="ac-row">
                    <span>Email address</span>
                    <b>{{ $user->email }}</b>
                </div>
                <div class="ac-row">
                    <span>Email verification</span>
                    @if ($verified)
                        <span class="ac-sw"><svg aria-hidden="true"><use href="#ai-chk"/></svg>Verified</span>
                    @else
                        <a class="ac-link" href="{{ route('verification.notice') }}">Not verified &mdash; verify now</a>
                    @endif
                </div>
                <a href="{{ route('account.settings') }}#security" class="ac-btn ac-fit"><svg aria-hidden="true"><use href="#ai-lock"/></svg>Review security</a>
            </div>
        </section>
    </div>

    <x-slot:scripts>
        <script>
        (function () {
            var reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
            document.querySelectorAll('[data-count]').forEach(function (el) {
                var t = +el.dataset.count, suf = el.dataset.suffix || '';
                if (reduce) { return; }
                var s = performance.now(), d = 1400;
                el.textContent = '0' + suf;
                (function tick(n) {
                    var p = Math.min(1, (n - s) / d);
                    el.textContent = Math.round(t * (1 - Math.pow(1 - p, 3))) + suf;
                    if (p < 1) { requestAnimationFrame(tick); }
                })(s);
            });
            var tt = document.getElementById('ac-toast'), tm;
            document.querySelectorAll('[data-copy]').forEach(function (b) {
                b.addEventListener('click', function () {
                    var done = function (msg) {
                        tt.textContent = msg; tt.classList.add('on');
                        clearTimeout(tm); tm = setTimeout(function () { tt.classList.remove('on'); }, 2400);
                    };
                    if (navigator.clipboard) {
                        navigator.clipboard.writeText(b.dataset.copy).then(function () { done(b.dataset.toast); }, function () { done('Copy failed. Select the link manually.'); });
                    } else { done('Copy is not supported in this browser.'); }
                });
            });
        })();
        </script>
    </x-slot:scripts>
</x-account.shell>
