@php
    /*
     * Decorative "power grid below the surface": a field of nodes joined by
     * flowing links, with office hubs that pulse and packets travelling
     * between them. Deterministic (no randomness at request time), inline
     * SVG, no JavaScript. Switched off from Admin → Configuration →
     * Visual Effects (network_animation_enabled).
     */
    $enabled = app(\Domain\Content\Repositories\SiteSettingRepository::class)->current()->networkAnimationEnabled;

    if ($enabled) {
        $hash = static function (int $i, int $seed = 0): float {
            $v = sin(($i + 1) * 12.9898 + $seed * 78.233) * 43758.5453;

            return $v - floor($v);
        };

        $hubs = [
            ['label' => 'Washington, D.C.', 'x' => 210, 'y' => 250],
            ['label' => 'Paris', 'x' => 700, 'y' => 215],
            ['label' => 'Abidjan', 'x' => 880, 'y' => 430],
            ['label' => 'Lagos', 'x' => 1080, 'y' => 360],
            ['label' => 'Douala', 'x' => 1300, 'y' => 470],
        ];

        // Field nodes on a jittered grid (skipping a share to keep it organic).
        $nodes = [];
        $cols = 15;
        $rows = 6;
        $i = 0;
        for ($r = 0; $r < $rows; $r++) {
            for ($c = 0; $c < $cols; $c++) {
                $i++;
                if ($hash($i, 3) < 0.28) {
                    continue;
                }
                $nodes[] = [
                    'x' => 50 + $c * (1500 / ($cols - 1)) + ($hash($i, 1) - 0.5) * 70,
                    'y' => 150 + $r * (400 / ($rows - 1)) + ($hash($i, 2) - 0.5) * 60,
                    'r' => 2.2 + $hash($i, 5) * 1.8,
                ];
            }
        }

        $all = array_merge(array_map(static fn ($h) => ['x' => $h['x'], 'y' => $h['y'], 'hub' => true], $hubs), $nodes);

        // Link each node to its two nearest neighbours; hubs to their four nearest.
        $links = [];
        $seen = [];
        foreach ($all as $a => $na) {
            $dist = [];
            foreach ($all as $b => $nb) {
                if ($a !== $b) {
                    $dist[$b] = hypot($na['x'] - $nb['x'], $na['y'] - $nb['y']);
                }
            }
            asort($dist);
            $take = ! empty($na['hub']) ? 4 : 2;
            foreach (array_slice(array_keys($dist), 0, $take) as $b) {
                $key = min($a, $b).'-'.max($a, $b);
                if (! isset($seen[$key])) {
                    $seen[$key] = true;
                    $links[] = ['a' => $na, 'b' => $all[$b], 'major' => ! empty($na['hub']) || ! empty($all[$b]['hub'])];
                }
            }
        }

        // Long arcs between consecutive hubs: the backbone.
        $arcs = [];
        for ($h = 0; $h < count($hubs) - 1; $h++) {
            $a = $hubs[$h];
            $b = $hubs[$h + 1];
            $mx = ($a['x'] + $b['x']) / 2;
            $my = min($a['y'], $b['y']) - 90;
            $arcs[] = 'M'.$a['x'].' '.$a['y'].' Q'.round($mx).' '.round($my).' '.$b['x'].' '.$b['y'];
        }

        // Packets travel along a selection of links and the backbone arcs.
        $packets = [];
        foreach ($arcs as $k => $d) {
            $packets[] = ['d' => $d, 'dur' => 4.5 + $k * 0.7, 'begin' => -($k * 1.3), 'r' => 3.6];
        }
        foreach (array_slice($links, 0, 150, true) as $k => $l) {
            if ($k % 6 !== 0) {
                continue;
            }
            $rev = $hash($k, 9) > 0.5;
            $from = $rev ? $l['b'] : $l['a'];
            $to = $rev ? $l['a'] : $l['b'];
            $packets[] = [
                'd' => 'M'.round($from['x']).' '.round($from['y']).' L'.round($to['x']).' '.round($to['y']),
                'dur' => 2.2 + $hash($k, 4) * 3.5,
                'begin' => -($hash($k, 6) * 5),
                'r' => 2.4,
            ];
        }
    }
@endphp

@if ($enabled)
    <div {{ $attributes->merge(['class' => 'net-bg pointer-events-none absolute inset-0 overflow-hidden']) }} aria-hidden="true">
        <svg class="h-full w-full text-gold" viewBox="0 0 1600 620" preserveAspectRatio="xMidYMid slice" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="net-surface" x1="0" x2="1" y1="0" y2="0">
                    <stop offset="0" stop-color="currentColor" stop-opacity="0" />
                    <stop offset=".5" stop-color="currentColor" stop-opacity=".9" />
                    <stop offset="1" stop-color="currentColor" stop-opacity="0" />
                </linearGradient>
            </defs>

            {{-- The surface --}}
            <line x1="0" y1="64" x2="1600" y2="64" stroke="url(#net-surface)" stroke-width="2" />
            <line x1="0" y1="72" x2="1600" y2="72" stroke="url(#net-surface)" stroke-width="1" opacity=".4" />

            {{-- Roots from the surface down to each hub --}}
            @foreach ($hubs as $hub)
                <line x1="{{ $hub['x'] }}" y1="64" x2="{{ $hub['x'] }}" y2="{{ $hub['y'] }}" stroke="currentColor" stroke-width="1" stroke-dasharray="2 6" opacity=".35" />
            @endforeach

            {{-- Field links --}}
            <g stroke="currentColor">
                @foreach ($links as $l)
                    <line class="net-link" x1="{{ round($l['a']['x'], 1) }}" y1="{{ round($l['a']['y'], 1) }}" x2="{{ round($l['b']['x'], 1) }}" y2="{{ round($l['b']['y'], 1) }}" stroke-width="{{ $l['major'] ? 1.6 : 1 }}" opacity="{{ $l['major'] ? .55 : .3 }}" />
                @endforeach
            </g>

            {{-- Backbone between offices --}}
            <g stroke="currentColor" stroke-width="2" opacity=".75">
                @foreach ($arcs as $d)
                    <path d="{{ $d }}" class="net-link" style="animation-duration:1.6s" />
                @endforeach
            </g>

            {{-- Field nodes --}}
            <g fill="currentColor" opacity=".7">
                @foreach ($nodes as $n)
                    <circle cx="{{ round($n['x'], 1) }}" cy="{{ round($n['y'], 1) }}" r="{{ round($n['r'], 1) }}" />
                @endforeach
            </g>

            {{-- Office hubs --}}
            @foreach ($hubs as $k => $hub)
                <g>
                    <circle class="net-pulse" cx="{{ $hub['x'] }}" cy="{{ $hub['y'] }}" r="10" stroke="currentColor" stroke-width="1.6" fill="none">
                        <animate attributeName="r" values="10;46" dur="3.6s" begin="{{ -($k * 0.9) }}s" repeatCount="indefinite" />
                        <animate attributeName="opacity" values=".7;0" dur="3.6s" begin="{{ -($k * 0.9) }}s" repeatCount="indefinite" />
                    </circle>
                    <circle cx="{{ $hub['x'] }}" cy="{{ $hub['y'] }}" r="9" fill="#0B0B0C" stroke="currentColor" stroke-width="2.4" />
                    <circle cx="{{ $hub['x'] }}" cy="{{ $hub['y'] }}" r="3.6" fill="currentColor" />
                    <text x="{{ $hub['x'] }}" y="{{ $hub['y'] + 30 }}" text-anchor="middle" font-size="13" letter-spacing="3" fill="currentColor" opacity=".8" style="font-family:ui-sans-serif,system-ui,sans-serif;font-weight:600;text-transform:uppercase">{{ $hub['label'] }}</text>
                </g>
            @endforeach

            {{-- Packets in flight --}}
            <g fill="currentColor">
                @foreach ($packets as $p)
                    <circle class="net-packet" r="{{ $p['r'] }}" opacity=".95">
                        <animateMotion dur="{{ round($p['dur'], 2) }}s" begin="{{ round($p['begin'], 2) }}s" repeatCount="indefinite" path="{{ $p['d'] }}" />
                    </circle>
                @endforeach
            </g>
        </svg>
    </div>
@endif
