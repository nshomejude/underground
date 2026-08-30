{{--
    The physical artifact of Underground membership: an ID-1 proportioned
    (~1.586:1) card rendered as a tangible object, front and back, with a
    pure-CSS flip (no JavaScript) triggered by a hidden checkbox + label pair.

    This component is intentionally decoupled from any Eloquent model so a
    real MembershipApplication (once approved and issued a permanent member
    id) can populate it later: pass its tier lookup, its name/organisation,
    and the issue/expiry pair computed at issuance time.

    Props:
    - variant            'individual' | 'organisation'
    - name               The member's full name, or the organisation's name
    - representative     Authorized representative's full name (organisation only)
    - representativeTitle  Their title, e.g. "Permanent Secretary" (optional)
    - tier               Domain\Membership\Entities\MembershipTier
    - memberId           Permanent member id, e.g. "UG · 2021 · 001147"
    - issuedOn           DateTimeInterface — start of the current card cycle
    - validThrough       DateTimeInterface — end of the current card cycle
    - status             Status label, default "Active"
    - statusTone         status-badge tone, default "success"
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
])

@php
    $isOrganisation = $variant === 'organisation';
    $flipId = 'membership-card-'.\Illuminate\Support\Str::random(10);
    $uvId = 'membership-card-uv-'.\Illuminate\Support\Str::random(10);

    $issuedLabel = strtoupper($issuedOn->format('M Y'));
    $validThroughLabel = strtoupper($validThrough->format('M Y'));

    // Each tier reads as the same family of card but is not interchangeable:
    // a distinct corner motif, border treatment, and foil intensity, built
    // entirely from the existing gold/ink palette — no new brand colours.
    $accents = [
        'sovereign-partner' => [
            'motif' => 'seal',
            'intensity' => 'opacity-[0.32]',
            'ring' => 'ring-2 ring-gold-bright/50',
            'innerRing' => 'before:absolute before:inset-[6px] before:rounded-xl before:border before:border-gold/30 before:pointer-events-none',
            'radius' => 'rounded-2xl',
        ],
        'principal-circle' => [
            'motif' => 'facet',
            'intensity' => 'opacity-[0.28]',
            'ring' => 'ring-2 ring-gold/45',
            'innerRing' => '',
            'radius' => 'rounded-2xl',
        ],
        'corporate-affiliate' => [
            'motif' => 'grid',
            'intensity' => 'opacity-[0.22]',
            'ring' => 'ring-2 ring-gold/40',
            'innerRing' => '',
            'radius' => 'rounded-xl',
        ],
    ];
    $accent = $accents[$tier->slug->value] ?? $accents['principal-circle'];

    // Deterministic "QR" placeholder: three finder squares (as real QR codes
    // carry) plus a seeded noise field, so it looks identical on every
    // render of the same member id rather than jittering per request.
    $qrModules = 19;
    $qrSeed = crc32($memberId);
    $isFinder = static function (int $r, int $c) use ($qrModules): bool {
        $inTopLeft = $r < 7 && $c < 7;
        $inTopRight = $r < 7 && $c >= $qrModules - 7;
        $inBottomLeft = $r >= $qrModules - 7 && $c < 7;

        return $inTopLeft || $inTopRight || $inBottomLeft;
    };
    $finderRing = static function (int $r, int $c, int $originR, int $originC): ?bool {
        $localR = $r - $originR;
        $localC = $c - $originC;
        if ($localR < 0 || $localR > 6 || $localC < 0 || $localC > 6) {
            return null;
        }
        $onOuterRing = $localR === 0 || $localR === 6 || $localC === 0 || $localC === 6;
        $onInnerBlock = $localR >= 2 && $localR <= 4 && $localC >= 2 && $localC <= 4;

        return $onOuterRing || $onInnerBlock;
    };

    // Guilloché security backdrop: a set of interlocking sine waves at
    // differing amplitude/frequency/phase so their crests interfere across
    // the card, the way engine-turned engraving does on currency and stock
    // certificates. This is a shared layer identical on every tier — the
    // tier motif above stays the only per-tier decoration.
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
            $y = round($baseline + $amplitude * sin($x * $frequency + $phase), 1);
            $points[] = round($x, 1).','.$y;
        }
        $guillocheWaves[] = 'M'.implode(' L', $points);
    }

    // Microprinted border strip: a genuinely tiny (4.5px) repeating phrase
    // that reads as fine texture rather than as text, the way currency and
    // ID microprinting does. Repeated well past any rendered card width so
    // the strip never runs out and shows a gap.
    $microtext = strtoupper(str_repeat('Underground Network · Authentic · ', 14));

    // UV-reactive verification code: deterministic per member id (stable
    // across renders) but invisible until the UV toggle lifts --uv to 1.
    $uvCode = 'UV·'.strtoupper(substr(hash('crc32b', $memberId.'|uv-ink'), 0, 8));
@endphp

<div
    {{ $attributes->merge(['class' => 'group/card relative mx-auto flex w-full max-w-[400px] flex-col items-center gap-4 select-none [perspective:1800px]']) }}
    role="group"
    aria-label="{{ $tier->name }} membership card for {{ $name }}"
>
    <input type="checkbox" id="{{ $flipId }}" class="peer/flip sr-only" aria-label="Flip the membership card to view {{ $isOrganisation ? $representative ?? $name : $name }}'s card back">

    {{-- A second hidden checkbox drives the UV-reactive ink simulation, using
         the same pure-CSS peer technique as the flip above rather than JS.
         Its target isn't only the sibling rotating element (which can react
         directly via peer-checked/uv) but also elements nested arbitrarily
         deep inside the front/back faces — CSS sibling combinators can't
         reach those, so peer-checked/uv sets a --uv custom property on the
         rotating element instead, and every descendant (regardless of
         nesting) reads it back via var(--uv, 0). --}}
    <input type="checkbox" id="{{ $uvId }}" class="peer/uv sr-only" aria-label="Inspect {{ $isOrganisation ? $representative ?? $name : $name }}'s card under UV light">

    {{-- The rotating element must be a direct sibling of the checkboxes
         above for the peer-checked selector to reach it (CSS's general
         sibling combinator only matches same-parent siblings) — so
         aspect-ratio sizing, 3D transform-style, and the flip transform all
         live on this one element rather than being split across nested
         wrappers. --}}
    <div class="relative aspect-[1.586/1] w-full transition-transform duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] [transform-style:preserve-3d] peer-checked/flip:[transform:rotateY(180deg)] peer-checked/uv:[--uv:1] peer-focus-visible/flip:outline peer-focus-visible/flip:outline-2 peer-focus-visible/flip:outline-offset-4 peer-focus-visible/flip:outline-gold">

            {{-- FRONT --}}
            <div class="absolute inset-0 overflow-hidden [backface-visibility:hidden] {{ $accent['radius'] }} {{ $accent['ring'] }} {{ $accent['innerRing'] }} shadow-[0_30px_70px_-20px_rgba(0,0,0,0.9),0_10px_25px_-10px_rgba(0,0,0,0.7)]">
                {{-- base metal gradient + top-left glint: a warmer, gold-tinted
                     charcoal so the card reads as its own object against the
                     page's near-black ground, not as a continuation of it --}}
                <div class="absolute inset-0 bg-[radial-gradient(130%_120%_at_12%_8%,color-mix(in_srgb,var(--color-gold-bright)_22%,transparent),transparent_48%),linear-gradient(155deg,color-mix(in_srgb,var(--color-surface-raised)_78%,var(--color-gold)_22%)_0%,var(--color-surface)_45%,var(--color-ink)_100%)]"></div>

                {{-- guilloché security backdrop: interlocking engraved waves,
                     identical across every tier (see the tier motif for the
                     per-tier decoration layered on top of it) --}}
                <svg viewBox="0 0 {{ $gW }} {{ $gH }}" preserveAspectRatio="none" class="pointer-events-none absolute inset-0 h-full w-full text-gold-bright opacity-[0.3] mix-blend-soft-light" fill="none" stroke="currentColor" stroke-width="0.85">
                    @foreach ($guillocheWaves as $d)
                        <path d="{{ $d }}" />
                    @endforeach
                </svg>

                {{-- engine-turned hairline texture --}}
                <div class="absolute inset-0 bg-[repeating-linear-gradient(135deg,rgba(201,162,90,0.09)_0px,rgba(201,162,90,0.09)_1px,transparent_1px,transparent_7px)] mix-blend-soft-light"></div>

                {{-- tier accent motif, etched into the top-right corner --}}
                @if ($accent['motif'] === 'seal')
                    <svg viewBox="0 0 200 200" class="pointer-events-none absolute -right-14 -top-14 h-56 w-56 text-gold-bright {{ $accent['intensity'] }}" fill="none" stroke="currentColor" stroke-width="1">
                        <circle cx="100" cy="100" r="92" />
                        <circle cx="100" cy="100" r="76" />
                        @for ($i = 0; $i < 36; $i++)
                            @php
                                $angle = deg2rad($i * 10);
                                $x1 = round(100 + 80 * cos($angle), 2);
                                $y1 = round(100 + 80 * sin($angle), 2);
                                $x2 = round(100 + 88 * cos($angle), 2);
                                $y2 = round(100 + 88 * sin($angle), 2);
                            @endphp
                            <line x1="{{ $x1 }}" y1="{{ $y1 }}" x2="{{ $x2 }}" y2="{{ $y2 }}" />
                        @endfor
                    </svg>
                @elseif ($accent['motif'] === 'facet')
                    <svg viewBox="0 0 200 200" class="pointer-events-none absolute -right-10 -top-10 h-52 w-52 text-gold-bright {{ $accent['intensity'] }}" fill="none" stroke="currentColor" stroke-width="1">
                        <path d="M100 6 L172 76 L138 194 L62 194 L28 76 Z" />
                        <path d="M100 6 L100 194" />
                        <path d="M28 76 L172 76" />
                        <path d="M100 6 L62 194" />
                        <path d="M100 6 L138 194" />
                        <path d="M28 76 L100 194" />
                        <path d="M172 76 L100 194" />
                    </svg>
                @else
                    <svg viewBox="0 0 200 200" class="pointer-events-none absolute -right-8 -top-8 h-48 w-48 text-gold-bright {{ $accent['intensity'] }}" fill="none" stroke="currentColor" stroke-width="1">
                        @for ($i = 0; $i <= 8; $i++)
                            @php $pos = $i * 25; @endphp
                            <line x1="0" y1="{{ $pos }}" x2="200" y2="{{ $pos }}" />
                            <line x1="{{ $pos }}" y1="0" x2="{{ $pos }}" y2="200" />
                        @endfor
                    </svg>
                @endif

                {{-- foil sheen: a diagonal glossy band that drifts on hover --}}
                <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(115deg,transparent_22%,rgba(224,190,126,0.28)_44%,rgba(255,255,255,0.22)_50%,rgba(224,190,126,0.28)_56%,transparent_78%)] mix-blend-overlay transition-transform duration-700 ease-out motion-safe:group-hover/card:translate-x-6"></div>

                {{-- top edge highlight, like light catching a bevelled rim --}}
                <div class="absolute inset-x-4 top-0 h-px bg-gradient-to-r from-transparent via-gold-bright to-transparent"></div>

                {{-- content --}}
                <div class="relative z-10 flex h-full flex-col justify-between p-4 pb-[22px] [filter:brightness(calc(1_-_(var(--uv,0)*0.45)))_saturate(calc(1_-_(var(--uv,0)*0.55)))] transition-[filter] duration-700">
                    <div class="flex items-start justify-between gap-3">
                        <span class="inline-flex items-center gap-2">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center border border-gold font-serif text-xs font-bold text-gold">U</span>
                            <span class="font-serif text-[13px] font-semibold tracking-wide text-cream">UNDERGROUND</span>
                        </span>

                        <div class="flex shrink-0 flex-col items-end gap-1.5">
                            <x-status-badge :label="$status" :tone="$statusTone" class="scale-[0.85] origin-top-right !px-2 !py-0.5 !text-[9px]" />

                            {{-- holographic security patch: an iridescent
                                 shield built only from color-mix() blends of
                                 the brand tokens, animated on
                                 background-position so its hue appears to
                                 shift with viewing angle the way real foil
                                 holograms do. --}}
                            <div
                                aria-hidden="true"
                                class="relative h-9 w-8 shrink-0 origin-top-right [clip-path:polygon(50%_0%,100%_22%,100%_60%,50%_100%,0%_60%,0%_22%)] bg-[conic-gradient(from_0deg,color-mix(in_srgb,var(--color-info)_62%,var(--color-gold-bright)_38%),color-mix(in_srgb,var(--color-success)_58%,var(--color-cream)_42%),color-mix(in_srgb,var(--color-cream)_70%,var(--color-gold-bright)_30%),color-mix(in_srgb,var(--color-gold)_55%,var(--color-success)_45%),color-mix(in_srgb,var(--color-info)_55%,var(--color-cream)_45%),color-mix(in_srgb,var(--color-gold-bright)_60%,var(--color-success)_40%),color-mix(in_srgb,var(--color-info)_62%,var(--color-gold-bright)_38%))] [background-size:220%_220%] shadow-[inset_0_0_0_1px_rgba(255,255,255,0.55),inset_0_1px_3px_rgba(255,255,255,0.6),0_1px_4px_rgba(0,0,0,0.5)] saturate-150 contrast-125 motion-safe:animate-[hologram-shift_6s_ease-in-out_infinite]"
                            >
                                <x-icon name="shield-check" class="absolute inset-0 m-auto h-4 w-4 text-ink mix-blend-overlay opacity-80" />
                                <div class="absolute inset-0 bg-[repeating-linear-gradient(115deg,rgba(255,255,255,0.4)_0px,rgba(255,255,255,0.4)_1px,transparent_1px,transparent_3px)] opacity-40 mix-blend-overlay"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-1 flex flex-col gap-2">
                        <span class="inline-flex items-center gap-2 text-gold">
                            <x-icon :name="$tier->icon" class="h-4 w-4 shrink-0" />
                            <span class="text-[10px] font-semibold uppercase tracking-[0.25em]">{{ $tier->name }}</span>
                        </span>

                        <div class="flex flex-col gap-0.5">
                            <span class="text-[9px] font-semibold uppercase tracking-[0.2em] text-muted">
                                {{ $isOrganisation ? 'Organisation' : 'Member' }}
                            </span>

                            @if ($isOrganisation)
                                <span class="line-clamp-2 font-serif text-lg font-semibold leading-tight tracking-wide text-cream [text-shadow:0_1px_1px_rgba(0,0,0,0.8),0_-1px_0_rgba(255,255,255,0.06)]">
                                    {{ $name }}
                                </span>
                                @if ($representative)
                                    <span class="mt-1.5 block truncate text-[11px] uppercase tracking-wide text-body/80">
                                        {{ $representative }}@if ($representativeTitle)<span class="text-muted"> &middot; {{ $representativeTitle }}</span>@endif
                                    </span>
                                @endif
                            @else
                                <span class="truncate font-serif text-xl font-semibold tracking-wide text-cream [text-shadow:0_1px_1px_rgba(0,0,0,0.8),0_-1px_0_rgba(255,255,255,0.06)]">
                                    {{ $name }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="mt-2 flex flex-col gap-2">
                        <div class="flex flex-col gap-1">
                            <span class="whitespace-nowrap text-[8px] font-semibold uppercase tracking-[0.2em] text-muted">Member ID</span>
                            <span class="whitespace-nowrap font-mono text-sm font-medium tabular-nums tracking-[0.1em] text-gold-bright [text-shadow:0_0_14px_rgba(224,190,126,0.35),0_1px_0_rgba(0,0,0,0.7)]">
                                {{ $memberId }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between gap-3 border-t border-border/40 pt-2">
                            <span class="whitespace-nowrap text-[8px] uppercase tracking-[0.12em] text-muted">
                                Issued <span class="font-mono tabular-nums text-body">{{ $issuedLabel }}</span>
                            </span>
                            <span class="whitespace-nowrap text-[8px] uppercase tracking-[0.12em] text-muted">
                                Valid Thru <span class="font-mono tabular-nums text-body">{{ $validThroughLabel }}</span>
                            </span>
                        </div>
                    </div>
                </div>

                {{-- UV-reactive ink: invisible in normal light, revealed by
                     the UV toggle. --uv is set to 1 on the rotating ancestor
                     when the UV checkbox is checked and inherits down to
                     here regardless of nesting depth, so a darkened wash
                     drops over the ordinary content (simulating house
                     lights dimmed for a UV lamp) while a repeating
                     watermark and a hidden verification code fluoresce on
                     top of it. --}}
                <div class="pointer-events-none absolute inset-0 z-20 bg-[color-mix(in_srgb,var(--color-ink)_62%,transparent)] opacity-[var(--uv,0)] transition-opacity duration-700"></div>

                <div class="pointer-events-none absolute inset-0 z-30 flex items-center justify-center overflow-hidden opacity-[var(--uv,0)] transition-opacity delay-100 duration-700">
                    <div class="grid w-[170%] -rotate-[26deg] grid-cols-3 gap-x-6 gap-y-7 motion-safe:animate-[uv-glow-pulse_2.6s_ease-in-out_infinite]">
                        @for ($i = 0; $i < 9; $i++)
                            <span class="whitespace-nowrap text-center font-serif text-[10px] font-bold uppercase tracking-[0.3em] text-info [text-shadow:0_0_6px_var(--color-info),0_0_14px_var(--color-info)]">Underground</span>
                        @endfor
                    </div>
                </div>

                <span class="pointer-events-none absolute bottom-[27px] left-4 z-30 whitespace-nowrap font-mono text-[9px] font-semibold tracking-[0.2em] text-gold-bright opacity-[var(--uv,0)] transition-opacity delay-100 duration-700 [text-shadow:0_0_6px_var(--color-gold-bright),0_0_12px_var(--color-gold-bright)] motion-safe:animate-[uv-glow-pulse_2.6s_ease-in-out_infinite]">
                    {{ $uvCode }}
                </span>

                {{-- microprinted border strip: a genuinely tiny repeating
                     phrase — texture to the naked eye, legible only under
                     magnification, exactly like currency and ID
                     microprinting. Shared across every tier. --}}
                <div class="pointer-events-none absolute inset-x-0 bottom-0 z-10 overflow-hidden bg-black/15 py-[2px]">
                    <span class="block whitespace-nowrap font-mono text-[4.5px] leading-none tracking-normal text-gold/40">
                        {{ $microtext }}
                    </span>
                </div>
            </div>

            {{-- BACK --}}
            <div class="absolute inset-0 overflow-hidden [backface-visibility:hidden] [transform:rotateY(180deg)] {{ $accent['radius'] }} {{ $accent['ring'] }} shadow-[0_30px_70px_-20px_rgba(0,0,0,0.9),0_10px_25px_-10px_rgba(0,0,0,0.7)]">
                <div class="absolute inset-0 bg-[radial-gradient(130%_120%_at_88%_92%,color-mix(in_srgb,var(--color-gold-bright)_16%,transparent),transparent_45%),linear-gradient(155deg,color-mix(in_srgb,var(--color-surface-raised)_78%,var(--color-gold)_22%)_0%,var(--color-surface)_45%,var(--color-ink)_100%)]"></div>

                {{-- guilloché security backdrop, shared with the front face --}}
                <svg viewBox="0 0 {{ $gW }} {{ $gH }}" preserveAspectRatio="none" class="pointer-events-none absolute inset-0 h-full w-full text-gold-bright opacity-[0.3] mix-blend-soft-light" fill="none" stroke="currentColor" stroke-width="0.85">
                    @foreach ($guillocheWaves as $d)
                        <path d="{{ $d }}" />
                    @endforeach
                </svg>

                <div class="absolute inset-0 bg-[repeating-linear-gradient(135deg,rgba(201,162,90,0.09)_0px,rgba(201,162,90,0.09)_1px,transparent_1px,transparent_7px)] mix-blend-soft-light"></div>

                {{-- microprinted border strip, mirrored from the front face --}}
                <div class="pointer-events-none absolute inset-x-0 top-0 z-10 overflow-hidden bg-black/15 py-[2px]">
                    <span class="block whitespace-nowrap font-mono text-[4.5px] leading-none tracking-normal text-gold/40">
                        {{ $microtext }}
                    </span>
                </div>

                <div class="relative z-10 flex h-full flex-col [filter:brightness(calc(1_-_(var(--uv,0)*0.45)))_saturate(calc(1_-_(var(--uv,0)*0.55)))] transition-[filter] duration-700">
                    {{-- magnetic stripe --}}
                    <div class="relative mt-5 h-8 w-full bg-black/85 shadow-[inset_0_1px_0_rgba(255,255,255,0.08)]">
                        <div class="absolute inset-x-0 top-1/2 h-px -translate-y-1/2 bg-gold/10"></div>
                    </div>

                    <div class="flex flex-1 items-center gap-3 px-4 py-3">
                        <div class="flex h-8 flex-1 items-center border border-border/70 bg-[repeating-linear-gradient(115deg,rgba(243,239,230,0.06)_0px,rgba(243,239,230,0.06)_2px,transparent_2px,transparent_5px)] px-3">
                            <span class="truncate font-serif text-[11px] italic text-body/80">{{ $isOrganisation ? $representative ?? $name : $name }}</span>
                        </div>

                        <div class="flex shrink-0 flex-col items-center gap-1">
                            <svg viewBox="0 0 {{ $qrModules }} {{ $qrModules }}" class="h-12 w-12 text-cream" shape-rendering="crispEdges">
                                <rect x="0" y="0" width="{{ $qrModules }}" height="{{ $qrModules }}" fill="var(--color-cream)" />
                                @for ($r = 0; $r < $qrModules; $r++)
                                    @for ($c = 0; $c < $qrModules; $c++)
                                        @php
                                            $fill = null;
                                            if (($ring = $finderRing($r, $c, 0, 0)) !== null) {
                                                $fill = $ring;
                                            } elseif (($ring = $finderRing($r, $c, 0, $qrModules - 7)) !== null) {
                                                $fill = $ring;
                                            } elseif (($ring = $finderRing($r, $c, $qrModules - 7, 0)) !== null) {
                                                $fill = $ring;
                                            } elseif (! $isFinder($r, $c)) {
                                                $fill = (crc32($qrSeed.'-'.$r.'-'.$c) % 5) < 2;
                                            }
                                        @endphp
                                        @if ($fill)
                                            <rect x="{{ $c }}" y="{{ $r }}" width="1" height="1" fill="var(--color-ink)" />
                                        @endif
                                    @endfor
                                @endfor
                            </svg>
                            <span class="inline-flex items-center gap-1 text-[7px] font-semibold uppercase tracking-widest text-muted">
                                <x-icon name="scan-line" class="h-2.5 w-2.5" /> Scan to Verify
                            </span>
                        </div>
                    </div>

                    <div class="mt-auto flex flex-col gap-1 border-t border-border/50 px-4 py-3">
                        <p class="text-[8px] leading-relaxed text-muted">
                            This card certifies membership in good standing with Underground Network and remains the
                            property of Underground Network Inc. Report loss immediately. Verify at
                            underground.network/verify.
                        </p>
                        <p class="text-[8px] font-semibold uppercase tracking-widest text-muted/70">
                            {{ $memberId }} &middot; {{ $tier->name }}
                        </p>
                    </div>
                </div>

                {{-- UV-reactive ink, back face: the same darkened wash plus a
                     fluorescing authenticity stamp over the signature panel --}}
                <div class="pointer-events-none absolute inset-0 z-20 bg-[color-mix(in_srgb,var(--color-ink)_62%,transparent)] opacity-[var(--uv,0)] transition-opacity duration-700"></div>

                <div class="pointer-events-none absolute inset-0 z-30 flex items-center justify-center opacity-[var(--uv,0)] transition-opacity delay-100 duration-700">
                    <span class="-rotate-[10deg] whitespace-nowrap font-serif text-lg font-bold uppercase tracking-[0.35em] text-info [text-shadow:0_0_8px_var(--color-info),0_0_18px_var(--color-info)] motion-safe:animate-[uv-glow-pulse_2.6s_ease-in-out_infinite]">
                        Authentic
                    </span>
                </div>
            </div>
        </div>

    <div class="flex items-center gap-4">
        <label for="{{ $flipId }}" class="peer-checked/flip:hidden inline-flex cursor-pointer items-center gap-1.5 text-[10px] font-semibold uppercase tracking-widest text-muted transition-colors hover:text-gold">
            <x-icon name="rotate-cw" class="h-3 w-3" /> View Back
        </label>
        <label for="{{ $flipId }}" class="hidden cursor-pointer items-center gap-1.5 text-[10px] font-semibold uppercase tracking-widest text-muted transition-colors hover:text-gold peer-checked/flip:inline-flex">
            <x-icon name="rotate-cw" class="h-3 w-3" /> View Front
        </label>

        <span class="h-3 w-px bg-border" aria-hidden="true"></span>

        {{-- UV toggle: a real interactive reveal, not a label saying a
             feature exists — checking it lifts --uv to 1 on the rotating
             card element above, which every hidden-ink layer reads back. --}}
        <label for="{{ $uvId }}" class="peer-checked/uv:hidden inline-flex cursor-pointer items-center gap-1.5 text-[10px] font-semibold uppercase tracking-widest text-muted transition-colors hover:text-info">
            <x-icon name="flashlight" class="h-3 w-3" /> Inspect Under UV
        </label>
        <label for="{{ $uvId }}" class="hidden cursor-pointer items-center gap-1.5 text-[10px] font-semibold uppercase tracking-widest text-info transition-colors hover:text-gold-bright peer-checked/uv:inline-flex">
            <x-icon name="flashlight" class="h-3 w-3" /> UV Light On
        </label>
    </div>
</div>
