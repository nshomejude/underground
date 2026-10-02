@php
    /*
     * "Power is built, block by block": isometric blocks that drop and lock
     * into a stepped pyramid (foundation, pillars, capstone). Inline SVG +
     * CSS; a few lines of JS in app.js play it on scroll. Switched off from
     * Admin → Configuration → Visual Effects (blocks_animation_enabled).
     */
    $enabled = app(\Domain\Content\Repositories\SiteSettingRepository::class)->current()->blocksAnimationEnabled;

    if ($enabled) {
        $w = 46.0;   // half tile width
        $h = 26.5;   // half tile height
        $s = 48.0;   // block height
        $ox = 280.0;
        $oy = 250.0;

        $palette = [
            ['#3A362F', '#26231F', '#17151A'], // foundation: top, left, right
            ['#6B5630', '#4B3C20', '#33281A'], // pillars
            ['#E0BE7E', '#C9A25A', '#9A7A3A'], // capstone
        ];

        $blocks = [];
        for ($k = 0; $k < 3; $k++) {
            $count = 3 - $k;
            for ($a = 0; $a < $count; $a++) {
                for ($b = 0; $b < $count; $b++) {
                    $blocks[] = ['layer' => $k, 'i' => $k * 0.5 + $a, 'j' => $k * 0.5 + $b];
                }
            }
        }
        usort($blocks, static fn ($p, $q) => [$p['layer'], $p['i'] + $p['j']] <=> [$q['layer'], $q['i'] + $q['j']]);

        $fmt = static fn (array $pts): string => implode(' ', array_map(static fn ($p) => round($p[0], 1).','.round($p[1], 1), $pts));
        $perLayer = [0 => 0, 1 => 0, 2 => 0];

        foreach ($blocks as $idx => $blk) {
            $cx = $ox + ($blk['i'] - $blk['j']) * $w;
            $cy = $oy + ($blk['i'] + $blk['j']) * $h - $blk['layer'] * $s;
            $n = $perLayer[$blk['layer']]++;
            $blocks[$idx] += [
                'delay' => round($blk['layer'] * 0.9 + $n * 0.09, 2),
                'top' => $fmt([[$cx, $cy - $s], [$cx + $w, $cy - $s + $h], [$cx, $cy - $s + 2 * $h], [$cx - $w, $cy - $s + $h]]),
                'left' => $fmt([[$cx - $w, $cy - $s + $h], [$cx, $cy - $s + 2 * $h], [$cx, $cy + 2 * $h], [$cx - $w, $cy + $h]]),
                'right' => $fmt([[$cx + $w, $cy - $s + $h], [$cx, $cy - $s + 2 * $h], [$cx, $cy + 2 * $h], [$cx + $w, $cy + $h]]),
                'cx' => $cx,
                'capY' => $cy - $s + $h,
            ];
        }

        $tiers = [
            ['Foundation', 'Discretion, trust and long-held relationships.'],
            ['Pillars', 'Government, capital, intelligence and media.'],
            ['Capstone', 'Influence that moves decisions.'],
        ];
    }
@endphp

@if ($enabled)
    <section data-power-blocks class="relative overflow-hidden border-b border-border bg-ink">
        <div class="mx-auto grid max-w-7xl grid-cols-1 items-center gap-10 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:gap-16 lg:px-8 lg:py-24">
            <div class="flex flex-col gap-8">
                <x-section-heading eyebrow="How Influence Is Built">
                    Power is built, block by block.
                </x-section-heading>

                <p class="max-w-lg text-base leading-relaxed text-body">
                    Lasting influence is never improvised. It is assembled in order: discretion and trust first,
                    then the institutions that carry weight, and only then the authority that moves a decision.
                </p>

                <ol class="flex flex-col gap-5">
                    @foreach ($tiers as $t => [$label, $text])
                        <li class="pb-tier flex items-start gap-4 border-l-2 border-gold/60 pl-4" style="--delay: {{ round($t * 0.9 + 1.0, 2) }}s">
                            <span class="min-w-[5.5rem] pt-0.5 text-[11px] font-semibold uppercase tracking-[0.2em] text-gold">{{ $label }}</span>
                            <span class="text-sm leading-relaxed text-body">{{ $text }}</span>
                        </li>
                    @endforeach
                </ol>
            </div>

            <div class="relative mx-auto w-full max-w-xl">
                <svg viewBox="0 0 560 470" class="h-auto w-full" role="img" aria-label="Isometric blocks stacking into a pyramid with a capstone labelled Power" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <radialGradient id="pb-halo" cx="50%" cy="50%" r="50%">
                            <stop offset="0" stop-color="#E0BE7E" stop-opacity=".55" />
                            <stop offset="1" stop-color="#E0BE7E" stop-opacity="0" />
                        </radialGradient>
                    </defs>
                    <ellipse cx="280" cy="400" rx="210" ry="38" fill="#C9A25A" opacity=".08" />
                    <g class="pb-scene">
                        @foreach ($blocks as $blk)
                            @php $c = $palette[$blk['layer']]; @endphp
                            <g class="pb-block" style="--delay: {{ $blk['delay'] }}s">
                                @if ($blk['layer'] === 2)
                                    <ellipse class="pb-glow" cx="{{ round($blk['cx'], 1) }}" cy="{{ round($blk['capY'], 1) }}" rx="130" ry="70" fill="url(#pb-halo)" />
                                @endif
                                <polygon points="{{ $blk['left'] }}" fill="{{ $c[1] }}" stroke="#C9A25A" stroke-opacity=".75" stroke-width="1.1" stroke-linejoin="round" />
                                <polygon points="{{ $blk['right'] }}" fill="{{ $c[2] }}" stroke="#C9A25A" stroke-opacity=".75" stroke-width="1.1" stroke-linejoin="round" />
                                <polygon points="{{ $blk['top'] }}" fill="{{ $c[0] }}" stroke="#C9A25A" stroke-opacity=".75" stroke-width="1.1" stroke-linejoin="round" />
                                @if ($blk['layer'] === 2)
                                    <text x="{{ round($blk['cx'], 1) }}" y="{{ round($blk['capY'] + 5, 1) }}" text-anchor="middle" font-size="13" letter-spacing="3" fill="#16140F" style="font-family:Georgia,serif;font-weight:700;text-transform:uppercase">Power</text>
                                @endif
                            </g>
                        @endforeach
                    </g>
                </svg>
            </div>
        </div>
    </section>
@endif
