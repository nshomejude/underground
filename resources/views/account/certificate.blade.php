@php
    use App\Support\Code128;
    use App\Support\Qr;

    $verifyUrl = $certificate->verificationUrl();
    $shortUrl = preg_replace('#^https?://#', '', $verifyUrl);
    [$shortHost, $shortToken] = [Str::before($shortUrl, '/verify/').'/verify/', Str::after($shortUrl, '/verify/')];
    $issued = $certificate->issued_at->format('j F Y');
    $through = $certificate->valid_through->format('j F Y');
    $longName = mb_strlen($holder) > 26;

    // A sine band as an SVG polyline point list. Horizontal bands run along x,
    // vertical bands along y; $c is the centre line.
    $band = static function (float $from, float $to, float $c, float $amp, float $period, float $phase, bool $vertical): string {
        $pts = [];
        for ($t = $from; $t <= $to; $t += 0.8) {
            $o = $c + $amp * sin(2 * M_PI * ($t - $from) / $period + $phase);
            $pts[] = $vertical ? sprintf('%.2f,%.2f', $o, $t) : sprintf('%.2f,%.2f', $t, $o);
        }

        return implode(' ', $pts);
    };

    // Hypotrochoid rosette centred on the origin (R/r = 11/3 closes after 3 turns).
    $rosette = static function (float $R, float $r, float $d, int $steps = 330): string {
        $pts = [];
        for ($i = 0; $i <= $steps; $i++) {
            $t = 6 * M_PI * $i / $steps;
            $x = ($R - $r) * cos($t) + $d * cos(($R - $r) / $r * $t);
            $y = ($R - $r) * sin($t) - $d * sin(($R - $r) / $r * $t);
            $pts[] = sprintf('%.2f,%.2f', $x, $y);
        }

        return implode(' ', $pts);
    };

    // Scalloped seal edge.
    $scallop = static function (float $radius, float $depth, int $lobes, int $steps = 360): string {
        $pts = [];
        for ($i = 0; $i < $steps; $i++) {
            $a = 2 * M_PI * $i / $steps;
            $rad = $radius + $depth * cos($lobes * $a);
            $pts[] = sprintf('%.2f,%.2f', $rad * cos($a), $rad * sin($a));
        }

        return implode(' ', $pts);
    };

    $sides = [
        ['h', 7, 19.2, 277.8], ['h', 203, 19.2, 277.8], ['v', 7, 19.2, 190.8], ['v', 290, 19.2, 190.8],
    ];
    $watermarkLine = str_repeat('UNDERGROUND · ', 16);
    $micro = str_repeat('UNDERGROUND NETWORK · AUTHENTIC · '.$certificate->serial.' · ', 6);
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <x-seo-head title="Certificate of Membership" description="Your Underground Network certificate of membership." :site-setting="app(\Domain\Content\Repositories\SiteSettingRepository::class)->current()" />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        :root { --paper:#F7F3EA; --ink:#0B0B0C; --label:#5C430F; --gold:#C9A25A; --gold2:#E0BE7E; --rule:#7a5d1c; --wm:.07; }
        .cert.dark { --paper:#0B0B0C; --ink:#F3EFE6; --label:#E0BE7E; --rule:#C9A25A; --wm:.10; }
        .cert-page { margin:0; background:#1b1a18; min-height:100vh; font-family:'Instrument Sans',system-ui,sans-serif; }
        .cert-page *, .cert-page *::before, .cert-page *::after { box-sizing:border-box; }
        .cert-ui { display:flex; gap:10px; justify-content:center; align-items:center; padding:14px; flex-wrap:wrap; }
        .cert-ui button, .cert-ui a { font:600 14px 'Instrument Sans',sans-serif; letter-spacing:.08em; text-transform:uppercase; padding:12px 20px; min-height:44px; border:1px solid #C9A25A; background:#0B0B0C; color:#E0BE7E; cursor:pointer; border-radius:2px; text-decoration:none; display:inline-flex; align-items:center; }
        .cert-ui button:hover, .cert-ui a:hover, .cert-ui button:focus-visible, .cert-ui a:focus-visible { background:#E0BE7E; color:#0B0B0C; outline:2px solid #F3EFE6; outline-offset:2px; }
        .stage { width:100%; overflow-x:auto; overflow-y:hidden; }
        .holder { width:297mm; height:210mm; transform-origin:top left; }
        .cert { position:relative; width:297mm; height:210mm; background:var(--paper); color:var(--ink); overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.6); }
        .cert svg.bg { position:absolute; inset:0; width:100%; height:100%; }
        .cert .gold { stroke:var(--gold); } .cert .ink { stroke:var(--ink); }
        .cert .paper { fill:var(--paper); stroke:var(--gold); stroke-width:.3; }
        .guil { fill:none; stroke-width:.12; } .guil.o { stroke:var(--gold); } .guil.i { stroke:var(--rule); stroke-opacity:.85; } .guil polyline { fill:none; }
        .wm text { font:600 2.6px 'Instrument Sans',sans-serif; letter-spacing:.3px; fill:var(--gold); opacity:var(--wm); }
        .mono { font:700 190px 'Playfair Display',serif; fill:var(--gold); opacity:.10; }
        .draw { animation:scalein 1.6s cubic-bezier(.2,.7,.2,1) both; transform-origin:50% 50%; }
        @keyframes scalein { from { opacity:0; transform:scale(.97); } to { opacity:1; transform:none; } }
        .content { position:absolute; inset:0; padding:24mm 30mm 0; display:flex; flex-direction:column; align-items:center; text-align:center; }
        .brand { font:600 10.5pt 'Instrument Sans'; letter-spacing:.42em; text-transform:uppercase; color:var(--label); margin:0; }
        .brand b { font-family:'Playfair Display',serif; letter-spacing:.3em; }
        .cert h1 { font:700 35pt/1.1 'Playfair Display',serif; margin:3mm 0 0; letter-spacing:.02em; color:var(--ink); }
        .foil { background:linear-gradient(100deg,#0B0B0C 0 42%,#8a6a1f 47%,#C9A25A 50%,#8a6a1f 53%,#0B0B0C 58% 100%); background-size:250% 100%; background-position:120% 0; -webkit-background-clip:text; background-clip:text; color:transparent !important; animation:sweep 6s ease-in-out 1.6s infinite; }
        .dark .foil { background-image:linear-gradient(100deg,#F3EFE6 0 42%,#C9A25A 47%,#fff 50%,#C9A25A 53%,#F3EFE6 58% 100%); }
        @keyframes sweep { 0%,55% { background-position:120% 0; } 100% { background-position:-20% 0; } }
        .orn { display:flex; align-items:center; gap:4mm; margin:2.5mm 0; width:110mm; }
        .orn i { flex:1; height:.3mm; background:linear-gradient(90deg,transparent,var(--gold),var(--gold)); } .orn i:last-child { transform:scaleX(-1); }
        .orn s { width:2.4mm; height:2.4mm; background:var(--gold); transform:rotate(45deg); }
        .lead { font:500 11pt 'Instrument Sans'; letter-spacing:.32em; text-transform:uppercase; color:var(--label); margin:1mm 0 0; }
        .name { font:500 36pt/1.1 'Playfair Display',serif; font-style:italic; margin:2mm 0 0; color:var(--ink); max-width:210mm; }
        .name.long { font-size:25pt; }
        .name::after { content:""; display:block; width:150mm; height:.3mm; margin:1.5mm auto 0; background:linear-gradient(90deg,transparent,var(--gold),transparent); }
        .body { font:400 11.5pt/1.5 'Instrument Sans'; max-width:170mm; margin:2.5mm 0 0; }
        .body strong { font-family:'Playfair Display',serif; font-size:13pt; letter-spacing:.04em; }
        .meta { display:grid; grid-template-columns:repeat(4,auto); margin-top:4mm; }
        .meta div { padding:0 7mm; border-left:.25mm solid var(--rule); } .meta div:first-child { border:0; }
        .k { display:block; font:600 9pt 'Instrument Sans'; letter-spacing:.24em; text-transform:uppercase; color:var(--label); }
        .v { display:block; font:500 12pt 'Playfair Display',serif; margin-top:1mm; color:var(--ink); } .v.id { font-family:'Instrument Sans'; font-weight:600; letter-spacing:.1em; font-size:11pt; white-space:nowrap; }
        .foot { position:absolute; left:30mm; right:30mm; bottom:21mm; display:grid; grid-template-columns:1fr 46mm 1fr; align-items:end; gap:6mm; }
        .sig { text-align:center; white-space:nowrap; }
        .sig .hand { font:italic 500 21pt/1 'Playfair Display',serif; color:var(--ink); height:12mm; display:block; }
        .sig .line { border-top:.3mm solid var(--rule); padding-top:1.4mm; }
        .sig .n { font:600 10pt 'Instrument Sans'; display:block; color:var(--ink); } .sig .r { font:500 9pt 'Instrument Sans'; letter-spacing:.12em; text-transform:uppercase; color:var(--label); }
        .sealwrap { position:absolute; left:50%; bottom:15mm; width:42mm; height:42mm; transform:translateX(-50%); }
        .sealwrap img { width:100%; height:100%; display:block; filter:drop-shadow(0 1mm 1.4mm rgba(0,0,0,.28)); }
        .qrbox { position:absolute; right:21mm; bottom:19mm; width:62mm; text-align:center; }
        .qrbg { background:#F7F3EA; padding:1.6mm; display:inline-block; outline:.25mm solid var(--rule); }
        .qr { width:24mm; height:24mm; display:block; } .qr svg { width:100%; height:100%; display:block; }
        .qrbox .t { font:600 9pt 'Instrument Sans'; display:block; margin-top:1.5mm; color:var(--ink); }
        .qrbox .u { font:500 7.5pt/1.3 'Instrument Sans'; color:var(--label); display:block; overflow-wrap:anywhere; }
        .serial { position:absolute; bottom:11.5mm; left:34mm; font:600 9pt 'Instrument Sans'; letter-spacing:.2em; text-transform:uppercase; color:var(--label); }
        .barcode { position:absolute; bottom:10.4mm; left:100mm; width:52mm; height:4.6mm; background:#F7F3EA; padding:.6mm 1mm; outline:.2mm solid var(--rule); }
        .barcode svg { width:100%; height:100%; display:block; }
        .micro { position:absolute; bottom:16.2mm; left:34mm; right:34mm; text-align:center; font:600 3.4pt 'Instrument Sans'; letter-spacing:.12em; color:var(--rule); white-space:nowrap; overflow:hidden; }
        .cert-note { color:#B9B4AC; text-align:center; font-size:13px; padding:0 16px 24px; }
        @media (prefers-reduced-motion:reduce) { .draw, .foil { animation:none !important; } .foil { background-position:-20% 0; } }
        @page { size:A4 landscape; margin:0; }
        @media print {
            html, body, .cert-page { background:#fff !important; min-height:0; }
            .cert-ui, .cert-note { display:none !important; }
            .stage { overflow:visible; height:auto !important; }
            .holder { transform:none !important; margin:0 !important; }
            .cert { box-shadow:none; }
            * { -webkit-print-color-adjust:exact; print-color-adjust:exact; animation:none !important; }
            .foil { background-position:-20% 0 !important; }
        }
    </style>
</head>
<body class="cert-page">
    <div class="cert-ui">
        <a href="{{ route('account.show') }}">&larr; Back to account</a>
        <button id="print" type="button">Print / save as PDF</button>
        <button id="tg" type="button" aria-pressed="false">Dark variant</button>
    </div>

    <main class="stage" id="stage">
        <div class="holder" id="holder">
            <div class="cert" id="cert">
                <svg class="bg" viewBox="0 0 297 210" preserveAspectRatio="none" aria-hidden="true">
                    <defs>
                        <linearGradient id="foil2" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#F6E3B0"/><stop offset=".55" stop-color="#C9A25A"/><stop offset="1" stop-color="#A9822F"/></linearGradient>
                        <g id="rosette">
                            <circle r="8.2" style="fill:var(--paper);stroke:var(--gold);stroke-width:.3"/>
                            <g style="fill:none;stroke:var(--rule);stroke-opacity:.85;stroke-width:.12"><polyline style="fill:none" points="{{ $rosette(7.7, 2.1, 2.9) }}"/></g>
                            <g style="fill:none;stroke:var(--gold);stroke-width:.12"><polyline style="fill:none" points="{{ $rosette(4.4, 1.2, 1.7) }}"/></g>
                        </g>
                    </defs>
                    <g class="wm" transform="rotate(-30 148.5 105)">
                        @for ($row = 0; $row < 44; $row++)
                            <text x="-40" y="{{ $row * 5 - 40 }}" textLength="420">{{ $watermarkLine }}</text>
                        @endfor
                    </g>
                    <text class="mono" x="148.5" y="158" text-anchor="middle">U</text>
                    <g class="draw">
                        <rect x="4.5" y="4.5" width="288" height="201" fill="none" class="gold" stroke-width="0.5"/>
                        <rect x="4" y="4" width="289" height="202" fill="none" class="gold" stroke-width="0.15"/>
                        <rect x="17.6" y="17.6" width="261.8" height="174.8" fill="none" class="gold" stroke-width="0.5"/>
                        <rect x="18.2" y="18.2" width="260.6" height="173.6" fill="none" class="ink" stroke-width="0.15"/>
                        @foreach ($sides as [$dir, $c, $from, $to])
                            @php($v = $dir === 'v')
                            <g class="guil o">
                                <polyline points="{{ $band($from, $to, $c, 2.4, 7.9, 0, $v) }}"/>
                                <polyline points="{{ $band($from, $to, $c, 2.4, 7.9, M_PI, $v) }}"/>
                            </g>
                            <g class="guil i"><polyline points="{{ $band($from, $to, $c, 1.5, 3.95, 0, $v) }}"/></g>
                        @endforeach
                        @foreach ([[11, 11], [286, 11], [11, 199], [286, 199]] as [$cx, $cy])
                            <use href="#rosette" x="{{ $cx }}" y="{{ $cy }}"/>
                        @endforeach
                    </g>
                </svg>

                <div class="content">
                    <p class="brand"><b>Underground</b> Network</p>
                    <h1 class="foil">Certificate of Membership</h1>
                    <div class="orn"><i></i><s></s><i></i></div>
                    <p class="lead">This certifies that</p>
                    <div class="name{{ $longName ? ' long' : '' }}">{{ $holder }}</div>
                    <p class="body">is a duly admitted member of the Underground Network, holding membership in the <strong>{{ $tierName }}</strong>, with all the privileges, access and obligations conferred by the Network's charter.</p>
                    <div class="meta">
                        <div><span class="k">Tier</span><span class="v">{{ $tierName }}</span></div>
                        <div><span class="k">Member ID</span><span class="v id">{{ $memberId }}</span></div>
                        <div><span class="k">Issued</span><span class="v">{{ $issued }}</span></div>
                        <div><span class="k">Valid through</span><span class="v">{{ $through }}</span></div>
                    </div>
                </div>

                <div class="foot">
                    <div class="sig"><span class="hand">Tony Smith</span><div class="line"><span class="n">Tony Smith</span><span class="r">Founder &amp; Managing Partner</span></div></div>
                    <div></div>
                    <div class="sig" style="margin-right:52mm"><span class="hand" aria-hidden="true">&nbsp;</span><div class="line"><span class="n">&nbsp;</span><span class="r">Membership Committee</span></div></div>
                </div>

                <div class="sealwrap">
                    <img src="{{ asset('images/seal/seal-gold@2x.png') }}" width="480" height="480" alt="Underground Network seal">
                </div>

                <div class="qrbox">
                    <span class="qrbg"><span class="qr" role="img" aria-label="QR code linking to {{ $shortUrl }}">{!! Qr::svg($verifyUrl) !!}</span></span>
                    <span class="t">Scan to verify authenticity</span>
                    <span class="u">{{ $shortHost }}<wbr>{{ $shortToken }}</span>
                </div>

                <div class="serial">Serial No. {{ $certificate->serial }}</div>
                <div class="barcode">{!! Code128::svg($certificate->serial, '#0B0B0C', 40) !!}</div>
                <div class="micro" aria-hidden="true">{{ $micro }}</div>
            </div>
        </div>
    </main>
    <p class="cert-note">Certificate of Membership for {{ $holder }}, {{ $tierName }}. Serial {{ $certificate->serial }}, valid through {{ $through }}. Anyone can verify it by scanning the QR code.</p>

    <script>
        (function () {
            var h = document.getElementById('holder'), st = document.getElementById('stage');
            function fit() {
                var s = Math.min(1.6, (innerWidth - 24) / h.offsetWidth);
                h.style.transform = 'scale(' + s + ')';
                st.style.overflow = 'hidden'; h.style.marginLeft = Math.max(0, (innerWidth - h.offsetWidth * s) / 2) + 'px';
                st.style.height = (h.offsetHeight * s + 24) + 'px';
            }
            addEventListener('resize', fit); fit();
            if (document.fonts) document.fonts.ready.then(fit);
            document.getElementById('print').onclick = function () { print(); };
            var tg = document.getElementById('tg');
            tg.onclick = function () {
                var d = document.getElementById('cert').classList.toggle('dark');
                tg.setAttribute('aria-pressed', d);
                tg.textContent = d ? 'Light variant' : 'Dark variant';
            };
        })();
    </script>
</body>
</html>
