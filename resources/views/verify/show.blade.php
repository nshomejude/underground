@php
    $steps = [
        ['label' => 'Reading credential', 'icon' => 'scan-line', 'work' => 'Parsing QR payload'],
        ['label' => 'Checking issuing registry', 'icon' => 'file-search', 'work' => 'Looking up serial'],
        ['label' => 'Validating credential seal', 'icon' => 'fingerprint', 'work' => 'Checking fingerprint'],
        ['label' => 'Confirming membership status', 'icon' => 'badge-check', 'work' => 'Reading standing'],
        ['label' => 'Cross-checking revocation list', 'icon' => 'link', 'work' => 'Checking list'],
    ];

    $fp = $facts['fingerprint'] ?? null;

    $states = [
        'valid' => [
            'col' => '#8FCB9B', 'title' => 'Credential verified', 'pill' => 'Valid', 'pillIcon' => 'badge-check',
            'text' => 'This credential was issued by Underground Network and is in good standing.',
            'guideTitle' => 'You can rely on this credential', 'guideIcon' => 'shield-check',
            'guide' => ['Compare the name and serial above with the card or certificate you were shown.', 'If they match, no further action is needed.'],
            'kinds' => ['done', 'done', 'done', 'done', 'done'],
            'detail' => ['Token parsed', 'Serial found', 'Fingerprint '.$fp, 'Active', 'Not listed'],
            'stat' => ['Done', 'Found', 'Valid', 'Active', 'Clear'],
            'status' => 'Active', 'through' => 'Valid through',
        ],
        'expired' => [
            'col' => '#E3A857', 'title' => 'Credential expired', 'pill' => 'Expired', 'pillIcon' => 'clock',
            'text' => 'This credential is genuine, but its validity period has ended.',
            'guideTitle' => 'Do not treat it as current', 'guideIcon' => 'triangle-alert',
            'guide' => ['Ask the holder for their renewed card or certificate.', 'You may confirm renewal with Underground Network using the Report a problem link below.'],
            'kinds' => ['done', 'done', 'done', 'warn', 'done'],
            'detail' => ['Token parsed', 'Serial found', 'Fingerprint '.$fp, 'Past valid-through date', 'Not listed'],
            'stat' => ['Done', 'Found', 'Valid', 'Expired', 'Clear'],
            'status' => 'Expired', 'through' => 'Expired on',
        ],
        'revoked' => [
            'col' => '#E57A6E', 'title' => 'Credential revoked', 'pill' => 'Revoked', 'pillIcon' => 'ban',
            'text' => 'Underground Network has withdrawn this credential. It must not be accepted.',
            'guideTitle' => 'Do not accept this credential', 'guideIcon' => 'ban',
            'guide' => ['Decline any benefit, access or recognition tied to it.', 'Do not confront the holder. Tell us through Report a problem so we can follow up.'],
            'kinds' => ['done', 'done', 'done', 'done', 'fail'],
            'detail' => ['Token parsed', 'Serial found', 'Fingerprint '.$fp, 'Record found', 'Listed as revoked'],
            'stat' => ['Done', 'Found', 'Valid', 'Found', 'Revoked'],
            'status' => 'Revoked', 'through' => 'Was valid through',
        ],
        'not_found' => [
            'col' => '#E57A6E', 'title' => 'No matching credential', 'pill' => 'Not found', 'pillIcon' => 'search-x',
            'text' => 'We could not find this credential in the Underground registry. It may be mistyped, damaged or not genuine.',
            'guideTitle' => 'Treat it as unverified', 'guideIcon' => 'circle-x',
            'guide' => ['Check the address bar reads un-der.com, then scan the code again.', 'If it still fails, do not accept the credential and report it.'],
            'kinds' => ['done', 'fail', 'skip', 'skip', 'skip'],
            'detail' => ['Token parsed', 'No record for token', 'Not checked', 'Not checked', 'Not checked'],
            'stat' => ['Done', 'Not found', 'Skipped', 'Skipped', 'Skipped'],
            'status' => 'Not found', 'through' => 'Valid through',
        ],
    ];
    $s = $states[$state];
    $resIcon = ['done' => 'check', 'warn' => 'triangle-alert', 'fail' => 'circle-x', 'skip' => 'info'];
    $maskedToken = strlen($token) > 8 ? substr($token, 0, 4).'…'.substr($token, -2) : substr($token, 0, 4).'…';
    $host = request()->getHost();
    $liveText = $s['title'].'. '.$s['text'].' '.$s['guideTitle'].'.';
@endphp

<x-layout title="Verify a Credential" description="Verify an Underground membership credential.">
    @include('verify._sprite')

    <style>
        .vf { --st: {{ $s['col'] }}; --ok:#8FCB9B; --warn:#E3A857; --bad:#E57A6E; --g:var(--color-gold, #C9A25A); --gb:var(--color-gold-bright, #E0BE7E); --cr:var(--color-cream, #F3EFE6); --bd:var(--color-border, #2A2825); --sf:var(--color-surface, #17161A); --rs:var(--color-surface-raised, #1F1E22); --ink:var(--color-ink, #0B0B0C); max-width:760px; margin:0 auto; padding:40px 16px 64px; color:var(--color-body, #B9B4AC); }
        .vf h1, .vf h2 { font-family:var(--font-serif); color:var(--cr); font-weight:600; margin:0; line-height:1.2; }
        .vf .ic { width:1.25em; height:1.25em; fill:none; stroke:currentColor; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; flex:none; }
        .vf .eyebrow { font-size:12px; letter-spacing:.18em; text-transform:uppercase; color:var(--gb); font-weight:600; }
        .vf :focus-visible { outline:3px solid var(--gb); outline-offset:3px; }
        .vf .sr { position:absolute; width:1px; height:1px; overflow:hidden; clip:rect(0 0 0 0); white-space:nowrap; }
        .vf .intro { text-align:center; margin-bottom:24px; }
        .vf .intro h1 { font-size:clamp(26px,5vw,38px); margin-top:8px; }
        .vf .intro p { margin:10px auto 0; max-width:48ch; }
        .vf .chips { display:flex; flex-wrap:wrap; gap:10px; justify-content:center; margin-top:14px; }
        .vf .chip { display:inline-flex; align-items:center; gap:8px; font:500 13px ui-monospace,Consolas,monospace; color:var(--cr); border:1px solid var(--bd); background:var(--sf); padding:8px 12px; min-height:44px; max-width:100%; overflow-wrap:anywhere; }
        .vf .chip .ic { color:var(--ok); }
        .vf .panel { border:1px solid var(--bd); background:var(--sf); }
        .vf .steps { list-style:none; margin:0; padding:8px 0; }
        .vf .step { display:grid; grid-template-columns:44px 1fr auto; align-items:center; gap:12px; padding:10px 16px; opacity:.55; transition:opacity .3s; }
        .vf .step.active, .vf .step.done, .vf .step.warn, .vf .step.fail, .vf .step.skip { opacity:1; }
        .vf .step.skip { opacity:.75; }
        .vf .sico { position:relative; width:40px; height:40px; border:1px solid var(--bd); display:grid; place-items:center; color:var(--color-body, #B9B4AC); background:var(--ink); }
        .vf .sico .ic { position:absolute; transition:transform .35s, opacity .3s; }
        .vf .sico .res { opacity:0; transform:scale(.3) rotate(-40deg); }
        .vf .step.active .sico { border-color:var(--g); color:var(--gb); }
        .vf .step.active .sico::after { content:""; position:absolute; inset:-1px; border:2px solid transparent; border-top-color:var(--gb); border-radius:50%; animation:vfspin .8s linear infinite; }
        .vf .step.done .sico, .vf .step.warn .sico, .vf .step.fail .sico, .vf .step.skip .sico { border-color:currentColor; }
        .vf .step.done .sico { color:var(--ok); } .vf .step.warn .sico { color:var(--warn); } .vf .step.fail .sico { color:var(--bad); }
        .vf .step.done .base, .vf .step.warn .base, .vf .step.fail .base, .vf .step.skip .base { opacity:0; transform:scale(.3) rotate(40deg); }
        .vf .step.done .res, .vf .step.warn .res, .vf .step.fail .res, .vf .step.skip .res { opacity:1; transform:none; }
        .vf .slabel { color:var(--cr); font-weight:500; display:block; }
        .vf .sdet { display:block; font-size:13.5px; overflow-wrap:anywhere; font-family:ui-monospace,Consolas,monospace; }
        .vf .stat { font-size:12px; letter-spacing:.14em; text-transform:uppercase; font-weight:600; text-align:right; }
        .vf .step.done .stat { color:var(--ok); } .vf .step.warn .stat { color:var(--warn); } .vf .step.fail .stat { color:var(--bad); }
        .vf .bar { height:2px; background:var(--bd); }
        .vf .bar i { display:block; height:100%; width:100%; background:var(--gb); }
        @keyframes vfspin { to { transform:rotate(360deg); } }
        @media (max-width:380px) { .vf .step { grid-template-columns:40px 1fr; padding:10px 12px; } .vf .stat { grid-column:2; text-align:left; } }
        .vf #result { margin-top:24px; }
        .vf.armed #result { display:none; }
        .vf .card { border:1px solid var(--st); background:var(--sf); position:relative; overflow:hidden; animation:vfrise .7s cubic-bezier(.2,.8,.2,1) both; }
        .vf .card::before { content:""; position:absolute; inset:0; background:radial-gradient(circle at 50% 0, color-mix(in srgb, var(--st) 16%, transparent), transparent 60%); pointer-events:none; }
        .vf .card.valid::after { content:""; position:absolute; top:0; bottom:0; width:40%; left:-60%; background:linear-gradient(100deg,transparent,rgba(224,190,126,.12),transparent); animation:vfsheen 1.6s .9s ease-out 1 both; pointer-events:none; }
        @keyframes vfsheen { to { left:130%; } }
        @keyframes vfrise { from { opacity:0; transform:translateY(18px); } to { opacity:1; transform:none; } }
        .vf .card-h { position:relative; text-align:center; padding:32px 20px 20px; }
        .vf .seal { width:132px; height:132px; margin:0 auto 16px; color:var(--st); display:block; overflow:visible; }
        .vf .seal .ring { fill:none; stroke:currentColor; stroke-width:3; stroke-dasharray:352; stroke-dashoffset:352; transform:rotate(-90deg); transform-origin:60px 60px; animation:vfdraw 1s .1s ease-out forwards; }
        .vf .seal .ring2 { fill:none; stroke:currentColor; stroke-width:1; opacity:.5; stroke-dasharray:4 5; transform-origin:60px 60px; animation:vffade .6s .8s both, vfspin 40s linear infinite; }
        .vf .seal .fill { fill:currentColor; opacity:0; animation:vffillin .6s .9s forwards; }
        .vf .seal .glyph { opacity:0; transform-origin:60px 60px; animation:vfstamp .5s .85s cubic-bezier(.2,1.6,.4,1) forwards; }
        .vf .seal .tick { fill:none; stroke:currentColor; stroke-width:5; stroke-linecap:round; stroke-linejoin:round; stroke-dasharray:60; stroke-dashoffset:60; animation:vfdraw .5s .85s ease-out forwards; }
        @keyframes vfdraw { to { stroke-dashoffset:0; } }
        @keyframes vffade { from { opacity:0; } to { opacity:.5; } }
        @keyframes vffillin { to { opacity:.1; } }
        @keyframes vfstamp { from { opacity:0; transform:scale(1.8); } to { opacity:1; transform:none; } }
        .vf .pill { display:inline-flex; align-items:center; gap:8px; border:1px solid var(--st); color:var(--st); padding:6px 14px; min-height:36px; font-size:13px; letter-spacing:.16em; text-transform:uppercase; font-weight:600; }
        .vf .card h2 { font-size:clamp(24px,5vw,32px); margin:14px 0 8px; }
        .vf .card-h p { margin:0 auto; max-width:52ch; }
        .vf .facts { position:relative; margin:0; display:grid; grid-template-columns:1fr 1fr; border-top:1px solid var(--bd); }
        .vf .facts div { padding:14px 20px; border-bottom:1px solid var(--bd); border-right:1px solid var(--bd); min-width:0; }
        .vf .facts div:nth-child(2n) { border-right:0; }
        .vf .facts dt { font-size:12px; letter-spacing:.16em; text-transform:uppercase; margin-bottom:2px; }
        .vf .facts dd { margin:0; color:var(--cr); font-size:17px; overflow-wrap:anywhere; }
        .vf .facts .mono { font-family:ui-monospace,Consolas,monospace; font-size:15px; }
        .vf .facts .wide { grid-column:1/-1; border-right:0; }
        .vf .facts dd .ic { vertical-align:-.2em; margin-right:6px; color:var(--st); }
        @media (max-width:480px) { .vf .facts { grid-template-columns:1fr; } .vf .facts div { border-right:0; } }
        .vf .stamp { position:relative; display:flex; flex-wrap:wrap; gap:10px 18px; align-items:center; padding:14px 20px; font-size:14px; border-bottom:1px solid var(--bd); }
        .vf .stamp .ic { color:var(--st); }
        .vf .stamp code { font-family:ui-monospace,Consolas,monospace; color:var(--cr); }
        .vf .copy { display:inline-flex; align-items:center; gap:8px; min-height:44px; min-width:44px; padding:0 12px; background:var(--rs); border:1px solid var(--bd); color:var(--cr); font:500 14px ui-monospace,Consolas,monospace; cursor:pointer; }
        .vf .copy:hover { border-color:var(--g); }
        .vf .guide { position:relative; padding:20px; display:flex; gap:14px; border-bottom:1px solid var(--bd); background:var(--rs); }
        .vf .guide .ic { width:24px; height:24px; color:var(--st); margin-top:2px; }
        .vf .guide h3 { font-size:18px; margin:0 0 4px; font-weight:600; color:var(--cr); }
        .vf .guide ul { margin:8px 0 0; padding-left:20px; list-style:disc; }
        .vf .actions { position:relative; display:flex; gap:12px; flex-wrap:wrap; align-items:center; padding:20px; }
        .vf .btn { display:inline-flex; align-items:center; justify-content:center; gap:10px; min-height:48px; padding:0 22px; border:1px solid var(--g); background:var(--g); color:var(--color-onlight, #16140F); font:600 15px var(--font-sans); letter-spacing:.06em; text-transform:uppercase; cursor:pointer; text-decoration:none; }
        .vf .btn:hover { background:var(--gb); }
        .vf .link { display:inline-flex; align-items:center; gap:8px; min-height:44px; color:var(--gb); text-underline-offset:4px; text-decoration:underline; }
        .vf .notes { display:grid; gap:12px; margin-top:20px; }
        .vf .note { display:flex; gap:12px; border:1px solid var(--bd); background:var(--sf); padding:14px 16px; font-size:15px; }
        .vf .note .ic { color:var(--gb); margin-top:3px; width:20px; height:20px; }
        .vf .note strong { color:var(--cr); font-weight:600; }
        .vf .note.phish { border-color:var(--g); }
        .vf .note p { margin:0; }
        @media (prefers-reduced-motion:reduce) { .vf *, .vf *::before, .vf *::after { animation-duration:.001s !important; animation-delay:0s !important; transition-duration:.001s !important; animation-iteration-count:1 !important; } }
    </style>

    <section class="vf" data-live="{{ $liveText }}" aria-labelledby="vf-h1">
        <script>
            (function (s) { if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) { s.parentNode.classList.add('armed'); } })(document.currentScript);
        </script>

        <div class="intro">
            <div class="eyebrow">Credential verification</div>
            <h1 id="vf-h1">{{ $s['title'] }}</h1>
            <p>Every Underground membership card and certificate carries a seal that can be checked against our registry.</p>
            <div class="chips">
                <span class="chip"><svg class="ic" aria-hidden="true"><use href="#i-lock"/></svg><span>{{ $host }}/verify/{{ $maskedToken }}</span></span>
                <span class="chip"><svg class="ic" aria-hidden="true"><use href="#i-qr-code"/></svg><span>Token {{ $maskedToken }}</span></span>
            </div>
        </div>

        <section class="panel" aria-label="Verification progress">
            <ol class="steps" id="steps">
                @foreach ($steps as $i => $step)
                    <li class="step {{ $s['kinds'][$i] }}" data-kind="{{ $s['kinds'][$i] }}" data-detail="{{ $s['detail'][$i] }}" data-stat="{{ $s['stat'][$i] }}" data-work="{{ $step['work'] }}">
                        <span class="sico">
                            <svg class="ic base" aria-hidden="true"><use href="#i-{{ $step['icon'] }}"/></svg>
                            <svg class="ic res" aria-hidden="true"><use href="#i-{{ $resIcon[$s['kinds'][$i]] }}"/></svg>
                        </span>
                        <span><span class="slabel">{{ $step['label'] }}</span><span class="sdet">{{ $s['detail'][$i] }}</span></span>
                        <span class="stat">{{ $s['stat'][$i] }}</span>
                    </li>
                @endforeach
            </ol>
            <div class="bar" aria-hidden="true"><i id="bar"></i></div>
        </section>

        <div class="sr" id="live" role="status" aria-live="polite" aria-atomic="true"></div>

        <section id="result" tabindex="-1" aria-label="Verification result">
            <article class="card {{ $state }}">
                <div class="card-h">
                    <svg class="seal" viewBox="0 0 120 120" aria-hidden="true">
                        <circle class="ring2" cx="60" cy="60" r="58"/><circle class="fill" cx="60" cy="60" r="56"/><circle class="ring" cx="60" cy="60" r="56"/>
                        @if ($state === 'valid')
                            <path class="tick" d="M38 62l14 14 30-32"/>
                        @else
                            <g class="glyph" transform="translate(30 30)"><svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><use href="#i-{{ ['expired' => 'clock', 'revoked' => 'ban', 'not_found' => 'search-x'][$state] }}"/></svg></g>
                        @endif
                    </svg>
                    <span class="pill"><svg class="ic" aria-hidden="true"><use href="#i-{{ $s['pillIcon'] }}"/></svg>{{ $s['pill'] }}</span>
                    <h2>{{ $s['title'] }}</h2>
                    <p>{{ $s['text'] }}</p>
                </div>

                <dl class="facts">
                    @if ($facts)
                        <div><dt>Holder</dt><dd>{{ $facts['name'] }}</dd></div>
                        <div><dt>Tier</dt><dd>{{ $facts['tier'] }}</dd></div>
                        <div><dt>Credential serial</dt><dd class="mono">{{ $facts['serial'] }}</dd></div>
                        <div><dt>Status</dt><dd><svg class="ic" aria-hidden="true"><use href="#i-{{ $s['pillIcon'] }}"/></svg>{{ $s['status'] }}</dd></div>
                        <div><dt>Issued</dt><dd>{{ $facts['issuedAt']->format('j M Y') }}</dd></div>
                        <div><dt>{{ $s['through'] }}</dt><dd>{{ $facts['validThrough']->format('j M Y') }}</dd></div>
                    @else
                        <div class="wide"><dt>Submitted token</dt><dd class="mono">{{ $maskedToken }}</dd></div>
                        <div class="wide"><dt>Result</dt><dd><svg class="ic" aria-hidden="true"><use href="#i-search-x"/></svg>No holder, tier or serial is shown because no record matched.</dd></div>
                    @endif
                </dl>

                <div class="stamp">
                    <svg class="ic" aria-hidden="true"><use href="#i-clock"/></svg>
                    <span>{{ $facts ? 'Verified' : 'Checked' }} just now at <time datetime="{{ $checkedAt->toIso8601String() }}">{{ $checkedAt->utc()->format('H:i:s') }} UTC</time></span>
                    @if ($fp)
                        <svg class="ic" aria-hidden="true"><use href="#i-fingerprint"/></svg>
                        <span id="fpwrap">Fingerprint <code id="fp">{{ $fp }}</code></span>
                    @endif
                </div>

                <div class="guide">
                    <svg class="ic" aria-hidden="true"><use href="#i-{{ $s['guideIcon'] }}"/></svg>
                    <div>
                        <h3>{{ $s['guideTitle'] }}</h3>
                        <ul>@foreach ($s['guide'] as $line)<li>{{ $line }}</li>@endforeach</ul>
                    </div>
                </div>

                <div class="actions">
                    <a class="btn" id="again" href="{{ url()->current() }}"><svg class="ic" aria-hidden="true"><use href="#i-refresh-cw"/></svg>Verify another credential</a>
                    <a class="link" href="{{ route('contact') }}"><svg class="ic" aria-hidden="true"><use href="#i-triangle-alert"/></svg>Report a problem</a>
                </div>
            </article>
        </section>

        <div class="notes">
            <div class="note phish"><svg class="ic" aria-hidden="true"><use href="#i-globe"/></svg><p><strong>Always confirm the address bar reads un-der.com.</strong> We never ask for passwords, payments or personal details on a verification page.</p></div>
            <div class="note"><svg class="ic" aria-hidden="true"><use href="#i-lock"/></svg><p>We show the minimum needed to verify. No contact details are ever displayed.</p></div>
        </div>
    </section>

    <script>
        (function () {
            var root = document.querySelector('.vf');
            if (!root) { return; }
            var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            var steps = [].slice.call(root.querySelectorAll('.step'));
            var result = root.querySelector('#result'), bar = root.querySelector('#bar'), live = root.querySelector('#live');
            var h1 = root.querySelector('#vf-h1'), finalTitle = h1.textContent, finalLive = root.getAttribute('data-live');
            var timers = [];
            function icon(kind) { return kind === 'fail' ? 'circle-x' : kind === 'warn' ? 'triangle-alert' : kind === 'skip' ? 'info' : 'check'; }
            function T(f, ms) { timers.push(setTimeout(f, ms)); }
            function done(el) {
                var kind = el.getAttribute('data-kind');
                el.className = 'step ' + kind;
                el.querySelector('.res use').setAttribute('href', '#i-' + icon(kind));
                el.querySelector('.sdet').textContent = el.getAttribute('data-detail');
                el.querySelector('.stat').textContent = el.getAttribute('data-stat');
            }
            function scramble(el, text) {
                var t0 = performance.now(), n = text.length, hex = '0123456789abcdef';
                (function f() {
                    var p = (performance.now() - t0) / 560;
                    if (p >= 1) { el.textContent = text; return; }
                    var s = ''; for (var i = 0; i < n; i++) { s += text.charAt(i).match(/[0-9a-f]/i) && i > 11 ? hex.charAt(Math.random() * 16 | 0) : text.charAt(i); }
                    el.textContent = s; requestAnimationFrame(f);
                })();
            }
            function reveal(focus) {
                bar.style.width = '100%';
                root.classList.remove('armed');
                h1.textContent = finalTitle;
                live.textContent = finalLive;
                if (focus) { result.focus({ preventScroll: false }); }
            }
            function run(focus) {
                timers.forEach(clearTimeout); timers = [];
                if (reduce) { steps.forEach(done); reveal(focus); return; }
                root.classList.add('armed');
                h1.textContent = 'Verifying credential';
                live.textContent = 'Verification started';
                bar.style.transition = 'none'; bar.style.width = '0';
                steps.forEach(function (el) {
                    el.className = 'step';
                    el.querySelector('.sdet').textContent = 'Waiting';
                    el.querySelector('.stat').textContent = 'Pending';
                });
                var per = 760;
                steps.forEach(function (el, i) {
                    T(function () {
                        el.className = 'step active';
                        el.querySelector('.stat').textContent = 'Working';
                        el.querySelector('.sdet').textContent = el.getAttribute('data-work');
                        bar.style.transition = 'width ' + per + 'ms linear';
                        bar.style.width = ((i + 1) / steps.length * 100) + '%';
                        if (i === 2 && el.getAttribute('data-kind') === 'done') { scramble(el.querySelector('.sdet'), el.getAttribute('data-detail')); }
                    }, i * per);
                    T(function () { done(el); }, i * per + per - 80);
                });
                T(function () { reveal(focus); }, steps.length * per + 250);
            }

            var again = root.querySelector('#again');
            again.addEventListener('click', function (e) { e.preventDefault(); run(true); });

            var fp = root.querySelector('#fp');
            if (fp && navigator.clipboard) {
                var b = document.createElement('button');
                b.type = 'button'; b.className = 'copy'; b.setAttribute('aria-label', 'Copy fingerprint');
                b.innerHTML = '<svg class="ic" aria-hidden="true"><use href="#i-copy"/></svg><span>Copy</span>';
                b.addEventListener('click', function () {
                    navigator.clipboard.writeText(fp.textContent).then(function () { b.lastChild.textContent = 'Copied'; live.textContent = 'Fingerprint copied'; });
                });
                root.querySelector('#fpwrap').appendChild(b);
            }

            if (root.classList.contains('armed')) { run(false); } else { live.textContent = finalLive; }
        })();
    </script>
</x-layout>
