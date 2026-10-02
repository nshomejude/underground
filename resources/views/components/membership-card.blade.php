{{--
    The physical artifact of Underground membership: an ID-1 proportioned
    (~1.586:1) card with a pure-CSS flip and a pure-CSS UV-light inspection
    mode (hidden checkboxes + labels, no JavaScript).

    Security features, and where they sit
    FRONT   guilloché engraving · microprint edge strips · holographic
            shield (right, middle) · embossed gold registrar SEAL (bottom
            right) · tier corner motif · UV: fluorescing monogram ring, UV
            code, UV seal ghost
    BACK    microprint strip · magnetic-stripe band · signature panel with
            authentication code · scannable QR code (right) · Code 128
            barcode of the credential serial (bottom) · UV: AUTHENTIC stamp,
            UV fibre lines, UV serial

    Props
    - variant            'individual' | 'organisation'
    - name               Member's full name, or the organisation's name
    - representative     Authorised representative (organisation only)
    - representativeTitle  Their title (optional)
    - tier               Domain\Membership\Entities\MembershipTier
    - memberId           Permanent member id, e.g. "UG · 2026 · 000001"
    - issuedOn / validThrough   DateTimeInterface
    - status / statusTone       Status label + status-badge tone
    - verifyUrl          Absolute URL encoded in the QR code
    - serial             Certificate serial printed + encoded in the barcode
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

    $issuedLabel = strtoupper($issuedOn->format('M Y'));
    $validThroughLabel = strtoupper($validThrough->format('M Y'));

    $verifyUrl ??= url('/verify/preview');
    $serial ??= 'UGC-'.$issuedOn->format('Y').'-'.substr(preg_replace('/\D/', '', $memberId), -6);
    $qrSvg = \App\Support\Qr::svg($verifyUrl);
    $barcodeSvg = \App\Support\Code128::svg($serial, '#0B0B0C', 36);
    $authCode = strtoupper(substr(hash('crc32b', $memberId.'|auth'), 0, 6));
    $uvCode = 'UV·'.strtoupper(substr(hash('crc32b', $memberId.'|uv-ink'), 0, 8));
    $shortUrl = preg_replace('#^https?://#', '', rtrim($verifyUrl, '/'));

    $accents = [
        'sovereign-partner' => ['motif' => 'seal', 'intensity' => 'opacity-[0.30]', 'ring' => 'ring-2 ring-gold-bright/50', 'radius' => 'rounded-2xl'],
        'principal-circle' => ['motif' => 'facet', 'intensity' => 'opacity-[0.26]', 'ring' => 'ring-2 ring-gold/45', 'radius' => 'rounded-2xl'],
        'corporate-affiliate' => ['motif' => 'grid', 'intensity' => 'opacity-[0.20]', 'ring' => 'ring-2 ring-gold/40', 'radius' => 'rounded-xl'],
    ];
    $accent = $accents[$tier->slug->value] ?? $accents['principal-circle'];

    // Guilloché engraving: interlocking sine waves, identical on every tier.
    $guillocheWaves = [];
    $gW = 400;
    $gH = 252;
    for ($w = 0; $w < 16; $w++) {
        $amplitude = 7 + ($w % 4) * 4;
        $frequency = 0.026 + ($w * 0.0032);
        $phase = $w * 0.55;
        $baseline = 6 + $w * 16;
        $points = [];
        for ($x = 0; $x <= $gW; $x += 6) {
            $points[] = $x.','.round($baseline + $amplitude * sin($x * $frequency + $phase), 1);
        }
        $guillocheWaves[] = 'M'.implode(' L', $points);
    }

    $microtext = strtoupper(str_repeat('Underground Network · Authentic · ', 14));

    // Scalloped rosette outline for the registrar seal.
    $scallop = '';
    $petals = 28;
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
    {{ $attributes->merge(['class' => 'group/card relative mx-auto flex w-full max-w-[480px] flex-col items-center gap-5 select-none [perspective:1800px]']) }}
    role="group"
    aria-label="{{ $tier->name }} membership card for {{ $holder }}"
>
    <input type="checkbox" id="{{ $flipId }}" class="peer/flip sr-only" aria-label="Flip the membership card to view the back">
    <input type="checkbox" id="{{ $uvId }}" class="peer/uv sr-only" aria-label="Inspect the card under UV light">

    <div class="relative aspect-[1.586/1] w-full transition-transform duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] [transform-style:preserve-3d] peer-checked/flip:[transform:rotateY(180deg)] peer-checked/uv:[--uv:1] peer-focus-visible/flip:outline peer-focus-visible/flip:outline-2 peer-focus-visible/flip:outline-offset-4 peer-focus-visible/flip:outline-gold">

        {{-- ============================== FRONT ============================== --}}
        <div class="absolute inset-0 overflow-hidden [backface-visibility:hidden] {{ $accent['radius'] }} {{ $accent['ring'] }} shadow-[0_30px_70px_-20px_rgba(0,0,0,0.9),0_10px_25px_-10px_rgba(0,0,0,0.7)]">
            <div class="absolute inset-0 bg-[radial-gradient(130%_120%_at_12%_8%,color-mix(in_srgb,var(--color-gold-bright)_22%,transparent),transparent_48%),linear-gradient(155deg,color-mix(in_srgb,var(--color-surface-raised)_78%,var(--color-gold)_22%)_0%,var(--color-surface)_45%,var(--color-ink)_100%)]"></div>

            <svg viewBox="0 0 {{ $gW }} {{ $gH }}" preserveAspectRatio="none" class="pointer-events-none absolute inset-0 h-full w-full text-gold-bright opacity-[0.28] mix-blend-soft-light" fill="none" stroke="currentColor" stroke-width="0.85" aria-hidden="true">
                @foreach ($guillocheWaves as $d)
                    <path d="{{ $d }}" />
                @endforeach
            </svg>
            <div class="absolute inset-0 bg-[repeating-linear-gradient(135deg,rgba(201,162,90,0.08)_0px,rgba(201,162,90,0.08)_1px,transparent_1px,transparent_7px)] mix-blend-soft-light"></div>

            {{-- tier corner motif --}}
            @if ($accent['motif'] === 'seal')
                <svg viewBox="0 0 200 200" class="pointer-events-none absolute -right-14 -top-14 h-56 w-56 text-gold-bright {{ $accent['intensity'] }}" fill="none" stroke="currentColor" stroke-width="1" aria-hidden="true">
                    <circle cx="100" cy="100" r="92" /><circle cx="100" cy="100" r="76" />
                    @for ($i = 0; $i < 36; $i++)
                        @php $a = deg2rad($i * 10); @endphp
                        <line x1="{{ round(100 + 80 * cos($a), 2) }}" y1="{{ round(100 + 80 * sin($a), 2) }}" x2="{{ round(100 + 88 * cos($a), 2) }}" y2="{{ round(100 + 88 * sin($a), 2) }}" />
                    @endfor
                </svg>
            @elseif ($accent['motif'] === 'facet')
                <svg viewBox="0 0 200 200" class="pointer-events-none absolute -right-10 -top-10 h-52 w-52 text-gold-bright {{ $accent['intensity'] }}" fill="none" stroke="currentColor" stroke-width="1" aria-hidden="true">
                    <path d="M100 6 L172 76 L138 194 L62 194 L28 76 Z" /><path d="M100 6 L100 194" /><path d="M28 76 L172 76" /><path d="M100 6 L62 194" /><path d="M100 6 L138 194" /><path d="M28 76 L100 194" /><path d="M172 76 L100 194" />
                </svg>
            @else
                <svg viewBox="0 0 200 200" class="pointer-events-none absolute -right-8 -top-8 h-48 w-48 text-gold-bright {{ $accent['intensity'] }}" fill="none" stroke="currentColor" stroke-width="1" aria-hidden="true">
                    @for ($i = 0; $i <= 8; $i++)
                        <line x1="0" y1="{{ $i * 25 }}" x2="200" y2="{{ $i * 25 }}" /><line x1="{{ $i * 25 }}" y1="0" x2="{{ $i * 25 }}" y2="200" />
                    @endfor
                </svg>
            @endif

            <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(115deg,transparent_22%,rgba(224,190,126,0.24)_44%,rgba(255,255,255,0.18)_50%,rgba(224,190,126,0.24)_56%,transparent_78%)] mix-blend-overlay transition-transform duration-700 ease-out motion-safe:group-hover/card:translate-x-6"></div>
            <div class="absolute inset-x-4 top-0 h-px bg-gradient-to-r from-transparent via-gold-bright to-transparent"></div>

            {{-- Front content: three rows so nothing can overlap --}}
            <div class="relative z-10 grid h-full grid-rows-[auto_1fr_auto] gap-2 px-5 pb-[26px] pt-[18px] [filter:brightness(calc(1_-_(var(--uv,0)*0.45)))_saturate(calc(1_-_(var(--uv,0)*0.55)))] transition-[filter] duration-700">
                <div class="flex items-start justify-between gap-3">
                    <span class="inline-flex items-center gap-2">
                        <span class="flex h-7 w-7 shrink-0 items-center justify-center border border-gold font-serif text-sm font-bold text-gold">U</span>
                        <span class="font-serif text-sm font-semibold tracking-wide text-cream">UNDERGROUND</span>
                    </span>
                    <x-status-badge :label="$status" :tone="$statusTone" class="origin-top-right scale-90 !px-2 !py-0.5 !text-[10px]" />
                </div>

                <div class="grid min-h-0 grid-cols-[minmax(0,1fr)_auto] items-center gap-3">
                    <div class="flex min-w-0 flex-col gap-1.5">
                        <span class="inline-flex items-center gap-2 text-gold">
                            <x-icon :name="$tier->icon" class="h-4 w-4 shrink-0" />
                            <span class="truncate text-[11px] font-semibold uppercase tracking-[0.22em]">{{ $tier->name }}</span>
                        </span>
                        <span class="truncate font-serif text-[1.35rem] font-semibold leading-tight tracking-wide text-cream [text-shadow:0_1px_1px_rgba(0,0,0,0.8)]">
                            {{ $name }}
                        </span>
                        @if ($isOrganisation && $representative)
                            <span class="truncate text-[11px] uppercase tracking-wide text-body">
                                {{ $representative }}@if ($representativeTitle) &middot; {{ $representativeTitle }}@endif
                            </span>
                        @endif
                        <span class="whitespace-nowrap font-mono text-[13px] font-medium tabular-nums tracking-[0.1em] text-gold-bright [text-shadow:0_0_14px_rgba(224,190,126,0.35)]">{{ $memberId }}</span>
                    </div>

                    {{-- holographic security shield --}}
                    <div
                        aria-hidden="true"
                        class="relative h-12 w-10 shrink-0 [clip-path:polygon(50%_0%,100%_22%,100%_60%,50%_100%,0%_60%,0%_22%)] bg-[conic-gradient(from_0deg,color-mix(in_srgb,var(--color-info)_62%,var(--color-gold-bright)_38%),color-mix(in_srgb,var(--color-success)_58%,var(--color-cream)_42%),color-mix(in_srgb,var(--color-cream)_70%,var(--color-gold-bright)_30%),color-mix(in_srgb,var(--color-gold)_55%,var(--color-success)_45%),color-mix(in_srgb,var(--color-info)_55%,var(--color-cream)_45%),color-mix(in_srgb,var(--color-gold-bright)_60%,var(--color-success)_40%),color-mix(in_srgb,var(--color-info)_62%,var(--color-gold-bright)_38%))] [background-size:220%_220%] shadow-[inset_0_0_0_1px_rgba(255,255,255,0.55),inset_0_1px_3px_rgba(255,255,255,0.6)] saturate-150 contrast-125 motion-safe:animate-[hologram-shift_6s_ease-in-out_infinite]"
                    >
                        <x-icon name="shield-check" class="absolute inset-0 m-auto h-5 w-5 text-ink mix-blend-overlay opacity-80" />
                        <div class="absolute inset-0 bg-[repeating-linear-gradient(115deg,rgba(255,255,255,0.4)_0px,rgba(255,255,255,0.4)_1px,transparent_1px,transparent_3px)] opacity-40 mix-blend-overlay"></div>
                    </div>
                </div>

                <div class="flex items-end justify-between gap-3 border-t border-border/50 pt-2">
                    <div class="flex gap-5">
                        <span class="flex flex-col gap-0.5 text-[10px] font-semibold uppercase tracking-[0.16em] text-body">
                            Issued
                            <span class="font-mono text-[12px] font-medium tabular-nums tracking-normal text-cream">{{ $issuedLabel }}</span>
                        </span>
                        <span class="flex flex-col gap-0.5 text-[10px] font-semibold uppercase tracking-[0.16em] text-body">
                            Valid thru
                            <span class="font-mono text-[12px] font-medium tabular-nums tracking-normal text-cream">{{ $validThroughLabel }}</span>
                        </span>
                    </div>

                    {{-- embossed registrar seal --}}
                    <svg viewBox="0 0 100 100" class="-mb-1 h-[3.75rem] w-[3.75rem] shrink-0 drop-shadow-[0_3px_5px_rgba(0,0,0,0.6)]" aria-hidden="true">
                        <defs>
                            <linearGradient id="{{ $sealId }}-g" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0" stop-color="#F6DFA6" /><stop offset=".45" stop-color="#C9A25A" /><stop offset="1" stop-color="#7A5C25" />
                            </linearGradient>
                            <path id="{{ $sealId }}-p" d="M50 50 m-31 0 a31 31 0 1 1 62 0 a31 31 0 1 1 -62 0" />
                        </defs>
                        <path d="{{ $scallop }}" fill="url(#{{ $sealId }}-g)" stroke="#F6DFA6" stroke-opacity=".6" stroke-width=".6" />
                        <circle cx="50" cy="50" r="38" fill="none" stroke="#3b2c0e" stroke-opacity=".55" stroke-width=".8" />
                        <circle cx="50" cy="50" r="24" fill="none" stroke="#3b2c0e" stroke-opacity=".5" stroke-width=".7" />
                        <text font-size="6.6" letter-spacing="1.5" fill="#2a1f08" style="font-family:ui-sans-serif,system-ui,sans-serif;font-weight:700"><textPath href="#{{ $sealId }}-p" startOffset="0">UNDERGROUND NETWORK · REGISTRAR · AUTHENTIC ·</textPath></text>
                        <text x="50" y="58.5" text-anchor="middle" font-size="25" fill="#2a1f08" style="font-family:Georgia,serif;font-weight:700">U</text>
                    </svg>
                </div>
            </div>

            {{-- UV (front) --}}
            <div class="pointer-events-none absolute inset-0 z-20 bg-[color-mix(in_srgb,var(--color-ink)_62%,transparent)] opacity-[var(--uv,0)] transition-opacity duration-700"></div>
            <div class="pointer-events-none absolute inset-0 z-30 flex items-center justify-center overflow-hidden opacity-[var(--uv,0)] transition-opacity delay-100 duration-700">
                <div class="grid w-[170%] -rotate-[26deg] grid-cols-3 gap-x-6 gap-y-7 motion-safe:animate-[uv-glow-pulse_2.6s_ease-in-out_infinite]">
                    @for ($i = 0; $i < 9; $i++)
                        <span class="whitespace-nowrap text-center font-serif text-[10px] font-bold uppercase tracking-[0.3em] text-info [text-shadow:0_0_6px_var(--color-info),0_0_14px_var(--color-info)]">Underground</span>
                    @endfor
                </div>
            </div>
            {{-- UV monogram ring, registered over the visible seal --}}
            <svg viewBox="0 0 100 100" class="pointer-events-none absolute bottom-[26px] right-5 z-30 -mb-1 h-[3.75rem] w-[3.75rem] text-info opacity-[var(--uv,0)] transition-opacity delay-100 duration-700 motion-safe:animate-[uv-glow-pulse_2.6s_ease-in-out_infinite]" fill="none" stroke="currentColor" aria-hidden="true">
                <circle cx="50" cy="50" r="46" stroke-width="2.4" /><circle cx="50" cy="50" r="36" stroke-width="1" stroke-dasharray="2 3" />
                <text x="50" y="62" text-anchor="middle" font-size="34" fill="currentColor" stroke="none" style="font-family:Georgia,serif;font-weight:700">U</text>
            </svg>
            <span class="pointer-events-none absolute bottom-[30px] left-5 z-30 whitespace-nowrap font-mono text-[10px] font-semibold tracking-[0.2em] text-gold-bright opacity-[var(--uv,0)] transition-opacity delay-100 duration-700 [text-shadow:0_0_6px_var(--color-gold-bright),0_0_12px_var(--color-gold-bright)]">{{ $uvCode }}</span>

            <div class="pointer-events-none absolute inset-x-0 bottom-0 z-10 overflow-hidden bg-black/20 py-[2px]">
                <span class="block whitespace-nowrap font-mono text-[4.5px] leading-none text-gold/50">{{ $microtext }}</span>
            </div>
        </div>

        {{-- ============================== BACK ============================== --}}
        <div class="absolute inset-0 overflow-hidden [backface-visibility:hidden] [transform:rotateY(180deg)] {{ $accent['radius'] }} {{ $accent['ring'] }} shadow-[0_30px_70px_-20px_rgba(0,0,0,0.9),0_10px_25px_-10px_rgba(0,0,0,0.7)]">
            <div class="absolute inset-0 bg-[radial-gradient(130%_120%_at_88%_92%,color-mix(in_srgb,var(--color-gold-bright)_16%,transparent),transparent_45%),linear-gradient(155deg,color-mix(in_srgb,var(--color-surface-raised)_78%,var(--color-gold)_22%)_0%,var(--color-surface)_45%,var(--color-ink)_100%)]"></div>
            <svg viewBox="0 0 {{ $gW }} {{ $gH }}" preserveAspectRatio="none" class="pointer-events-none absolute inset-0 h-full w-full text-gold-bright opacity-[0.26] mix-blend-soft-light" fill="none" stroke="currentColor" stroke-width="0.85" aria-hidden="true">
                @foreach ($guillocheWaves as $d)
                    <path d="{{ $d }}" />
                @endforeach
            </svg>
            <div class="pointer-events-none absolute inset-x-0 top-0 z-10 overflow-hidden bg-black/20 py-[2px]">
                <span class="block whitespace-nowrap font-mono text-[4.5px] leading-none text-gold/50">{{ $microtext }}</span>
            </div>

            <div class="relative z-10 flex h-full flex-col [filter:brightness(calc(1_-_(var(--uv,0)*0.45)))_saturate(calc(1_-_(var(--uv,0)*0.55)))] transition-[filter] duration-700">
                {{-- magnetic stripe --}}
                <div class="mt-[18px] h-[18%] w-full bg-black/90 shadow-[inset_0_1px_0_rgba(255,255,255,0.08)]"></div>

                {{-- signature panel (left) + QR (right) --}}
                <div class="grid flex-1 grid-cols-[minmax(0,1fr)_auto] items-center gap-4 px-5 py-2">
                    <div class="flex min-w-0 flex-col gap-1.5">
                        <span class="text-[10px] font-semibold uppercase tracking-[0.18em] text-body">Authorised signature</span>
                        <div class="flex h-9 items-center justify-between gap-2 border border-border/80 bg-[repeating-linear-gradient(115deg,rgba(243,239,230,0.07)_0px,rgba(243,239,230,0.07)_2px,transparent_2px,transparent_5px)] px-3">
                            <span class="truncate font-serif text-[13px] italic text-cream">{{ $holder }}</span>
                            <span class="shrink-0 font-mono text-[10px] tracking-widest text-gold-bright">{{ $authCode }}</span>
                        </div>
                        <span class="truncate font-mono text-[10px] tracking-[0.12em] text-body">{{ $memberId }}</span>
                    </div>

                    <div class="flex shrink-0 flex-col items-center gap-1">
                        <div class="h-[4.75rem] w-[4.75rem] bg-cream p-1 [&>svg]:h-full [&>svg]:w-full">{!! $qrSvg !!}</div>
                        <span class="inline-flex items-center gap-1 text-[9px] font-semibold uppercase tracking-widest text-body">
                            <x-icon name="scan-line" class="h-3 w-3" /> Scan to verify
                        </span>
                    </div>
                </div>

                {{-- barcode strip + serial + legal --}}
                <div class="flex flex-col gap-1.5 px-5 pb-3">
                    <div class="flex items-center gap-3 bg-cream px-3 py-1">
                        <div class="h-7 min-w-0 flex-1 [&>svg]:h-full [&>svg]:w-full">{!! $barcodeSvg !!}</div>
                        <span class="shrink-0 font-mono text-[10px] font-semibold tracking-[0.12em] text-ink">{{ $serial }}</span>
                    </div>
                    <p class="text-[9px] leading-snug text-body">
                        Property of Underground Network Inc. Non-transferable. Verify at {{ $shortUrl }}. Report loss by secure inquiry.
                    </p>
                </div>
            </div>

            {{-- UV (back) --}}
            <div class="pointer-events-none absolute inset-0 z-20 bg-[color-mix(in_srgb,var(--color-ink)_62%,transparent)] opacity-[var(--uv,0)] transition-opacity duration-700"></div>
            <div class="pointer-events-none absolute inset-0 z-30 flex items-center justify-center opacity-[var(--uv,0)] transition-opacity delay-100 duration-700">
                <span class="-rotate-[10deg] whitespace-nowrap font-serif text-xl font-bold uppercase tracking-[0.35em] text-info [text-shadow:0_0_8px_var(--color-info),0_0_18px_var(--color-info)] motion-safe:animate-[uv-glow-pulse_2.6s_ease-in-out_infinite]">Authentic</span>
            </div>
            <span class="pointer-events-none absolute bottom-3 right-5 z-30 whitespace-nowrap font-mono text-[10px] font-semibold tracking-[0.18em] text-info opacity-[var(--uv,0)] transition-opacity delay-100 duration-700 [text-shadow:0_0_6px_var(--color-info)]">{{ $serial }}</span>
        </div>
    </div>

    <div class="flex flex-wrap items-center justify-center gap-x-5 gap-y-2">
        <label for="{{ $flipId }}" class="inline-flex cursor-pointer items-center gap-1.5 text-[11px] font-semibold uppercase tracking-widest text-body transition-colors hover:text-gold peer-checked/flip:hidden">
            <x-icon name="rotate-cw" class="h-3.5 w-3.5" /> View Back
        </label>
        <label for="{{ $flipId }}" class="hidden cursor-pointer items-center gap-1.5 text-[11px] font-semibold uppercase tracking-widest text-body transition-colors hover:text-gold peer-checked/flip:inline-flex">
            <x-icon name="rotate-cw" class="h-3.5 w-3.5" /> View Front
        </label>

        <span class="hidden h-3 w-px bg-border sm:block" aria-hidden="true"></span>

        <label for="{{ $uvId }}" class="inline-flex cursor-pointer items-center gap-1.5 text-[11px] font-semibold uppercase tracking-widest text-body transition-colors hover:text-info peer-checked/uv:hidden">
            <x-icon name="flashlight" class="h-3.5 w-3.5" /> Inspect Under UV
        </label>
        <label for="{{ $uvId }}" class="hidden cursor-pointer items-center gap-1.5 text-[11px] font-semibold uppercase tracking-widest text-info transition-colors hover:text-gold-bright peer-checked/uv:inline-flex">
            <x-icon name="flashlight" class="h-3.5 w-3.5" /> UV Light On
        </label>
    </div>
</div>
