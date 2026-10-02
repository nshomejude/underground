@php
    /*
     * Decorative "clockwork" background: a diagonal chain of meshed gold gears
     * (tooth phases are solved so neighbours genuinely interlock), a dimmer
     * larger layer behind for depth, and a faint clock dial for "time".
     * Pure inline SVG + CSS rotation — no JavaScript. Switched off from
     * Admin → Configuration → Visual Effects (gear_animation_enabled).
     */
    $enabled = app(\Domain\Content\Repositories\SiteSettingRepository::class)->current()->gearAnimationEnabled;

    if ($enabled) {
        $module = 3.5;

        // Closed path for a spur gear with $n teeth about the origin.
        $gearPath = static function (int $n, float $module): string {
            $pitch = $module * $n / 2;
            $ro = $pitch + $module;
            $ri = $pitch - 1.25 * $module;
            $step = 2 * M_PI / $n;
            $d = '';
            for ($i = 0; $i < $n; $i++) {
                $a = $i * $step;
                $pts = [
                    [$ri, $a - $step * 0.30],
                    [$ro, $a - $step * 0.15],
                    [$ro, $a + $step * 0.15],
                    [$ri, $a + $step * 0.30],
                ];
                foreach ($pts as $k => [$r, $ang]) {
                    $d .= ($i === 0 && $k === 0 ? 'M' : 'L').round($r * cos($ang), 1).' '.round($r * sin($ang), 1);
                }
                $root = $a + $step * 0.70;
                $d .= 'A'.round($ri, 1).' '.round($ri, 1).' 0 0 1 '.round($ri * cos($root), 1).' '.round($ri * sin($root), 1);
            }

            return $d.'Z';
        };

        // Front chain: teeth counts and the bearing (degrees, y-down) from each gear to the next.
        $teeth = [50, 34, 58, 38, 52, 30, 60, 36, 54, 32, 46, 40, 64, 42];
        $bearings = [-33, -19, -40, -23, -36, -17, -38, -21, -35, -26, -31, -24, -37];

        $front = [];
        $x = -90.0;
        $y = 940.0;
        $rot = 0.0; // degrees
        foreach ($teeth as $i => $n) {
            $pitch = $module * $n / 2;
            if ($i > 0) {
                $prev = $front[$i - 1];
                $phi = deg2rad($bearings[$i - 1]);
                $dist = $prev['pitch'] + $pitch;
                $x = $prev['x'] + $dist * cos($phi);
                $y = $prev['y'] + $dist * sin($phi);
                // Solve this gear's phase so a tooth of the previous gear meets a gap here.
                $stepB = 2 * M_PI / $n;
                $rotA = deg2rad($prev['rot']);
                $rotB = $phi + M_PI - $stepB / 2 - ($prev['pitch'] * ($rotA - $phi)) / $pitch;
                $rot = rad2deg($rotB);
            }
            $front[] = [
                'n' => $n,
                'x' => $x,
                'y' => $y,
                'pitch' => $pitch,
                'rot' => $rot,
                'dir' => $i % 2 === 0 ? 1 : -1,
                'spokes' => [6, 5, 8, 6, 7, 5, 8, 6, 7, 5, 6, 7, 8, 6][$i],
                'path' => $gearPath($n, $module),
                // Seconds per revolution: small gears turn faster, meshed pairs stay in ratio.
                'dur' => round($n * 1.35, 1),
            ];
        }

        // Back layer: bigger module, dim and slow, overlapping the front chain for depth.
        $backModule = 6.5;
        $back = [
            ['n' => 44, 'x' => 470, 'y' => 520, 'dir' => 1, 'dur' => 150],
            ['n' => 36, 'x' => 1180, 'y' => 150, 'dir' => -1, 'dur' => 120],
            ['n' => 40, 'x' => 1400, 'y' => 700, 'dir' => 1, 'dur' => 135],
        ];
        foreach ($back as $i => $b) {
            $back[$i]['pitch'] = $backModule * $b['n'] / 2;
            $back[$i]['path'] = $gearPath($b['n'], $backModule);
        }

        // Clock dial (time) behind everything.
        $dial = ['x' => 1010, 'y' => 380, 'r' => 210];
    }
@endphp

@if ($enabled)
    <div {{ $attributes->merge(['class' => 'gear-bg pointer-events-none absolute inset-0 -z-0 overflow-hidden']) }} aria-hidden="true">
        <svg class="absolute left-1/2 top-1/2 h-[860px] w-[1600px] max-w-none -translate-x-1/2 -translate-y-1/2 lg:static lg:h-full lg:w-full lg:translate-x-0 lg:translate-y-0" viewBox="0 0 1600 860" preserveAspectRatio="xMidYMid slice" fill="none" xmlns="http://www.w3.org/2000/svg">
            {{-- Time: dial with tick marks and slow hands --}}
            <g opacity=".5" stroke="currentColor" class="text-gold">
                <circle cx="{{ $dial['x'] }}" cy="{{ $dial['y'] }}" r="{{ $dial['r'] }}" stroke-width="1.6" />
                <circle cx="{{ $dial['x'] }}" cy="{{ $dial['y'] }}" r="{{ $dial['r'] - 34 }}" stroke-width="1" opacity=".6" />
                @for ($t = 0; $t < 60; $t++)
                    @php
                        $big = $t % 5 === 0;
                        $a = deg2rad($t * 6);
                        $r1 = $dial['r'] - ($big ? 34 : 16);
                    @endphp
                    <line
                        x1="{{ round($dial['x'] + $r1 * cos($a), 1) }}" y1="{{ round($dial['y'] + $r1 * sin($a), 1) }}"
                        x2="{{ round($dial['x'] + $dial['r'] * cos($a), 1) }}" y2="{{ round($dial['y'] + $dial['r'] * sin($a), 1) }}"
                        stroke-width="{{ $big ? 3 : 1.2 }}" opacity="{{ $big ? .9 : .5 }}"
                    />
                @endfor
                <g transform="translate({{ $dial['x'] }} {{ $dial['y'] }})">
                    <g class="gear-spin" style="--d:1440s">
                        <line x1="0" y1="0" x2="0" y2="-{{ $dial['r'] * 0.5 }}" stroke-width="7" stroke-linecap="round" />
                    </g>
                    <g class="gear-spin" style="--d:120s">
                        <line x1="0" y1="0" x2="0" y2="-{{ $dial['r'] * 0.8 }}" stroke-width="3.5" stroke-linecap="round" />
                    </g>
                    <circle r="11" fill="#0B0B0C" stroke-width="3" />
                </g>
            </g>

            {{-- Depth layer --}}
            <g opacity=".34" stroke="currentColor" class="text-gold">
                @foreach ($back as $b)
                    <g transform="translate({{ $b['x'] }} {{ $b['y'] }})">
                        <g class="gear-spin" style="--d:{{ $b['dur'] }}s;--dir:{{ $b['dir'] === 1 ? 'normal' : 'reverse' }}">
                            <path d="{{ $b['path'] }}" stroke-width="3" stroke-linejoin="round" />
                            <circle r="{{ round($b['pitch'] * 0.78, 1) }}" stroke-width="1.6" opacity=".6" />
                            @for ($s = 0; $s < 8; $s++)
                                @php $sa = deg2rad($s * 45); @endphp
                                <line x1="{{ round($b['pitch'] * 0.2 * cos($sa), 1) }}" y1="{{ round($b['pitch'] * 0.2 * sin($sa), 1) }}" x2="{{ round($b['pitch'] * 0.78 * cos($sa), 1) }}" y2="{{ round($b['pitch'] * 0.78 * sin($sa), 1) }}" stroke-width="5" stroke-linecap="round" opacity=".8" />
                            @endfor
                            <circle r="{{ round($b['pitch'] * 0.2, 1) }}" fill="#0B0B0C" stroke-width="3" />
                        </g>
                    </g>
                @endforeach
            </g>

            {{-- Front chain: meshed gears climbing diagonally --}}
            <g stroke="currentColor" class="text-gold" opacity=".72">
                @foreach ($front as $g)
                    <g transform="translate({{ round($g['x'], 1) }} {{ round($g['y'], 1) }})">
                        <g class="gear-spin" style="--d:{{ $g['dur'] }}s;--dir:{{ $g['dir'] === 1 ? 'normal' : 'reverse' }}">
                            <g transform="rotate({{ round($g['rot'], 2) }})">
                                <path d="{{ $g['path'] }}" stroke-width="2" stroke-linejoin="round" fill="rgba(201,162,90,.07)" />
                                <circle r="{{ round($g['pitch'] * 0.72, 1) }}" stroke-width="1.4" opacity=".6" />
                                @for ($s = 0; $s < $g['spokes']; $s++)
                                    @php $sa = deg2rad($s * 360 / $g['spokes']); @endphp
                                    <line x1="{{ round($g['pitch'] * 0.2 * cos($sa), 1) }}" y1="{{ round($g['pitch'] * 0.2 * sin($sa), 1) }}" x2="{{ round($g['pitch'] * 0.72 * cos($sa), 1) }}" y2="{{ round($g['pitch'] * 0.72 * sin($sa), 1) }}" stroke-width="{{ max(3, round($g['pitch'] * 0.07, 1)) }}" stroke-linecap="round" opacity=".85" />
                                @endfor
                                <circle r="{{ round($g['pitch'] * 0.2, 1) }}" fill="#0B0B0C" stroke-width="2.6" />
                                <circle r="{{ round($g['pitch'] * 0.07, 1) }}" fill="currentColor" stroke="none" />
                            </g>
                        </g>
                    </g>
                @endforeach
            </g>
        </svg>
    </div>
@endif
