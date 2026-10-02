{{--
    The Underground membership card: an ID-1 (1.586:1) credential with a pure-CSS
    flip and a pure-CSS UV-light inspection mode (hidden checkboxes + labels).
    Styles live in resources/css/membership-card.css; the card scales as one
    piece using container-query units.

    Where the security features sit
    FRONT  animated foil edge · engine-turned guilloché · microprint strip
           (bottom edge) · registrar SEAL with moving hologram sheen (bottom
           right) · tier finish (platinum / gold / bronze)
           UV: fluorescing UNDERGROUND field, UV monogram ring registered over
           the seal, UV code (bottom left)
    BACK   microprint strip (top edge) · magnetic-stripe band · signature
           panel + authentication code · scannable QR code (right) · Code 128
           barcode of the serial (bottom)
           UV: AUTHENTIC stamp, UV serial

    Props: variant, name, representative, representativeTitle, tier, memberId,
    issuedOn, validThrough, status, statusTone, verifyUrl, serial.
    Icons are Lucide (lucide.dev), via <x-icon>.
--}}
@props([
    'variant' => 'individual',
    'name',
    'representative' => null,
    'representativeTitle' => null,
    'tier',
    'memberId',
    'issuedOn',
    'validThrough',
    'status' => 'Active',
    'statusTone' => 'success',
    'verifyUrl' => null,
    'serial' => null,
])

@php
    $isOrganisation = $variant === 'organisation';
    $holder = $isOrganisation ? ($representative ?? $name) : $name;
    $flipId = 'membership-card-'.\Illuminate\Support\Str::random(10);
    $uvId = 'membership-card-uv-'.\Illuminate\Support\Str::random(10);

    $finish = match ($tier->slug->value) {
        'sovereign-partner' => 'sovereign',
        'corporate-affiliate' => 'corporate',
        default => 'principal',
    };

    $since = strtoupper($issuedOn->format('M Y'));
    $thru = strtoupper($validThrough->format('m / Y'));

    $verifyUrl ??= url('/verify/preview');
    $serial ??= 'UGC-'.$issuedOn->format('Y').'-'.substr(preg_replace('/\D/', '', $memberId), -6);
    $qrSvg = \App\Support\Qr::svg($verifyUrl);
    $barcodeSvg = \App\Support\Code128::svg($serial, '#0B0B0C', 36);
    $authCode = strtoupper(substr(hash('crc32b', $memberId.'|auth'), 0, 6));
    $uvCode = 'UV·'.strtoupper(substr(hash('crc32b', $memberId.'|uv-ink'), 0, 8));
    $shortUrl = preg_replace('#^https?://#', '', rtrim($verifyUrl, '/'));

    // Engine-turned guilloché engraving shared by both faces.
    $waves = [];
    for ($w = 0; $w < 14; $w++) {
        $pts = [];
        for ($x = 0; $x <= 400; $x += 8) {
            $pts[] = $x.','.round(126 + sin($x / 38 + $w * 0.5) * (18 + $w * 5), 1);
        }
        $waves[] = 'M'.implode(' L', $pts);
    }

    $microtext = strtoupper(str_repeat('Underground Network · Authentic · ', 16));

    // Scalloped rosette outline for the registrar seal.
    $scallop = '';
    $petals = 30;
    for ($i = 0; $i < $petals; $i++) {
        $a1 = deg2rad($i * 360 / $petals);
        $a2 = deg2rad(($i + 1) * 360 / $petals);
        $am = ($a1 + $a2) / 2;
        $scallop .= ($i === 0 ? 'M' : 'L').round(50 + 44 * cos($a1), 2).' '.round(50 + 44 * sin($a1), 2);
        $scallop .= 'Q'.round(50 + 50 * cos($am), 2).' '.round(50 + 50 * sin($am), 2).' '.round(50 + 44 * cos($a2), 2).' '.round(50 + 44 * sin($a2), 2);
    }
    $scallop .= 'Z';
    $sealId = 'seal-'.\Illuminate\Support\Str::random(6);
@endphp

<div
    {{ $attributes->merge(['class' => "mc mc-tier-{$finish} group/card relative mx-auto flex w-full max-w-[520px] flex-col items-center gap-5 select-none [perspective:1800px]"]) }}
    role="group"
    aria-label="{{ $tier->name }} membership card for {{ $holder }}"
>
    <input type="checkbox" id="{{ $flipId }}" class="peer/flip sr-only" aria-label="Flip the membership card to view the back">
    <input type="checkbox" id="{{ $uvId }}" class="peer/uv sr-only" aria-label="Inspect the card under UV light">

    <div class="relative aspect-[1.586/1] w-full transition-transform duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] [transform-style:preserve-3d] peer-checked/flip:[transform:rotateY(180deg)] peer-checked/uv:[--uv:1] peer-focus-visible/flip:outline peer-focus-visible/flip:outline-2 peer-focus-visible/flip:outline-offset-4 peer-focus-visible/flip:outline-gold">

        {{-- ================================ FRONT ================================ --}}
        <div class="mc-face">
            <svg class="mc-guilloche" viewBox="0 0 400 252" preserveAspectRatio="xMidYMid slice" fill="none" stroke="currentColor" stroke-width=".8" aria-hidden="true">
                @foreach ($waves as $d)
                    <path d="{{ $d }}" />
                @endforeach
            </svg>

            @if ($finish === 'sovereign')
                <svg class="mc-motif" viewBox="0 0 200 200" fill="none" stroke="currentColor" stroke-width="1" aria-hidden="true">
                    <circle cx="100" cy="100" r="92" /><circle cx="100" cy="100" r="76" />
                    @for ($i = 0; $i < 36; $i++)
                        @php $a = deg2rad($i * 10); @endphp
                        <line x1="{{ round(100 + 80 * cos($a), 2) }}" y1="{{ round(100 + 80 * sin($a), 2) }}" x2="{{ round(100 + 88 * cos($a), 2) }}" y2="{{ round(100 + 88 * sin($a), 2) }}" />
                    @endfor
                </svg>
            @elseif ($finish === 'corporate')
                <svg class="mc-motif" viewBox="0 0 200 200" fill="none" stroke="currentColor" stroke-width="1" aria-hidden="true">
                    @for ($i = 0; $i <= 8; $i++)
                        <line x1="0" y1="{{ $i * 25 }}" x2="200" y2="{{ $i * 25 }}" /><line x1="{{ $i * 25 }}" y1="0" x2="{{ $i * 25 }}" y2="200" />
                    @endfor
                </svg>
            @else
                <svg class="mc-motif" viewBox="0 0 200 200" fill="none" stroke="currentColor" stroke-width="1" aria-hidden="true">
                    <path d="M100 6 L172 76 L138 194 L62 194 L28 76 Z" /><path d="M100 6 L100 194" /><path d="M28 76 L172 76" /><path d="M100 6 L62 194" /><path d="M100 6 L138 194" /><path d="M28 76 L100 194" /><path d="M172 76 L100 194" />
                </svg>
            @endif

            <div class="mc-front">
                <div class="mc-row">
                    <span class="mc-chip">
                        <x-icon :name="$tier->icon" />
                        <span class="truncate">{{ $tier->name }}</span>
                    </span>
                    <span class="mc-mono" aria-hidden="true">U</span>
                </div>

                <div class="flex min-h-0 flex-col justify-end pb-[1cqw]">
                    <span class="mc-id">{{ $memberId }}</span>
                    <span class="mc-name {{ $isOrganisation ? 'mc-name-wrap' : '' }}">{{ $name }}</span>
                    @if ($isOrganisation && $representative)
                        <span class="mc-sub">{{ $representative }}@if ($representativeTitle) &middot; {{ $representativeTitle }}@endif</span>
                    @endif
                </div>

                <div class="mc-row items-end">
                    <div class="mc-meta">
                        <div><span>Member since</span><b>{{ $since }}</b></div>
                        <div><span>Valid thru</span><b>{{ $thru }}</b></div>
                    </div>
                    <div class="w-[14.5cqw] shrink-0" aria-hidden="true"></div>
                </div>
            </div>

            {{-- registrar seal --}}
            <svg class="mc-seal" viewBox="0 0 100 100" aria-hidden="true">
                <defs>
                    <linearGradient id="{{ $sealId }}-g" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0" stop-color="var(--mc-foil-b)" /><stop offset=".5" stop-color="var(--mc-foil-c)" /><stop offset="1" stop-color="var(--mc-foil-a)" />
                    </linearGradient>
                    <path id="{{ $sealId }}-p" d="M50 50 m-30 0 a30 30 0 1 1 60 0 a30 30 0 1 1 -60 0" />
                </defs>
                <path d="{{ $scallop }}" fill="url(#{{ $sealId }}-g)" stroke="var(--mc-foil-b)" stroke-opacity=".6" stroke-width=".6" />
                <circle cx="50" cy="50" r="38" fill="none" stroke="#1c1608" stroke-opacity=".55" stroke-width=".8" />
                <circle cx="50" cy="50" r="23" fill="none" stroke="#1c1608" stroke-opacity=".5" stroke-width=".7" />
                <text font-size="6.4" letter-spacing="1.4" fill="#1c1608" style="font-family:ui-sans-serif,system-ui,sans-serif;font-weight:700"><textPath href="#{{ $sealId }}-p">UNDERGROUND NETWORK · REGISTRAR · AUTHENTIC ·</textPath></text>
                <text x="50" y="59" text-anchor="middle" font-size="26" fill="#1c1608" style="font-family:Georgia,serif;font-weight:700">U</text>
            </svg>

            <div class="mc-micro" style="bottom:0"><span>{{ $microtext }}</span></div>

            {{-- UV (front) --}}
            <div class="mc-uv-wash"></div>
            <div class="mc-uv mc-uv-field"><div>@for ($i = 0; $i < 9; $i++)<span>Underground</span>@endfor</div></div>
            <svg class="mc-uv mc-seal" style="z-index:7;filter:none;animation:uv-glow-pulse 2.6s ease-in-out infinite" viewBox="0 0 100 100" fill="none" stroke="currentColor" aria-hidden="true">
                <circle cx="50" cy="50" r="46" stroke-width="2.4" /><circle cx="50" cy="50" r="36" stroke-width="1" stroke-dasharray="2 3" />
                <text x="50" y="62" text-anchor="middle" font-size="34" fill="currentColor" stroke="none" style="font-family:Georgia,serif;font-weight:700">U</text>
            </svg>
            <span class="mc-uv" style="left:6cqw;bottom:5.8cqw;color:var(--mc-accent);font:600 clamp(9.5px,2.2cqw,12px) ui-monospace,Consolas,monospace;letter-spacing:.2em">{{ $uvCode }}</span>
        </div>

        {{-- ================================ BACK ================================ --}}
        <div class="mc-face is-back">
            <svg class="mc-guilloche" viewBox="0 0 400 252" preserveAspectRatio="xMidYMid slice" fill="none" stroke="currentColor" stroke-width=".8" aria-hidden="true">
                @foreach ($waves as $d)
                    <path d="{{ $d }}" />
                @endforeach
            </svg>
            <div class="mc-micro" style="top:0"><span>{{ $microtext }}</span></div>

            <div class="mc-back">
                <div class="mc-stripe"></div>

                <div class="mc-mid">
                    <div class="min-w-0">
                        <span class="mc-label">Authorised signature</span>
                        <div class="mc-sign"><i>{{ $holder }}</i><code>{{ $authCode }}</code></div>
                        <span class="mc-verify">Verify at <b>{{ $shortUrl }}</b></span>
                    </div>
                    <div>
                        <div class="mc-qr">{!! $qrSvg !!}</div>
                        <span class="mc-scan"><x-icon name="scan-line" /> Scan to verify</span>
                    </div>
                </div>

                <div class="mc-barcode">
                    <div>{!! $barcodeSvg !!}</div>
                    <code>{{ $serial }}</code>
                </div>
            </div>

            {{-- UV (back) --}}
            <div class="mc-uv-wash"></div>
            <div class="mc-uv mc-uv-field"><span style="font-size:clamp(14px,4.4cqw,22px);transform:rotate(-10deg);letter-spacing:.35em">Authentic</span></div>
            <span class="mc-uv" style="right:6cqw;bottom:1.6cqw;font:600 clamp(9.5px,2.2cqw,12px) ui-monospace,Consolas,monospace;letter-spacing:.14em">{{ $serial }}</span>
        </div>
    </div>

    <div class="mc-controls">
        <label for="{{ $flipId }}" class="inline-flex peer-checked/flip:hidden"><x-icon name="rotate-cw" /> View Back</label>
        <label for="{{ $flipId }}" class="hidden peer-checked/flip:inline-flex"><x-icon name="rotate-cw" /> View Front</label>
        <label for="{{ $uvId }}" class="inline-flex peer-checked/uv:hidden"><x-icon name="flashlight" /> Inspect Under UV</label>
        <label for="{{ $uvId }}" class="hidden !text-info peer-checked/uv:inline-flex"><x-icon name="flashlight" /> UV Light On</label>
    </div>
</div>
