@php
    /*
     * Decorative Washington, D.C. vignette: a stylised dome, colonnade and
     * obelisk on the Mall axis, with a sunburst radiating from the dome and
     * a scatter of five-point stars — the same engraved gold line-art
     * vocabulary as the gear and network backgrounds, so "the seat of
     * power" reads as one more mechanism in the same machine. Pure inline
     * SVG + CSS — no JavaScript.
     */
    $groundY = 680;

    // Five-point star polygon, point-up, centred at (x, y).
    $star = static function (float $x, float $y, float $ro, float $ri): string {
        $pts = [];
        for ($i = 0; $i < 10; $i++) {
            $a = -M_PI / 2 + $i * M_PI / 5;
            $r = $i % 2 === 0 ? $ro : $ri;
            $pts[] = round($x + $r * cos($a), 1).','.round($y + $r * sin($a), 1);
        }

        return 'M'.implode(' L', $pts).'Z';
    };

    $stars = [
        $star(300, 180, 11, 4.3),
        $star(1380, 220, 9, 3.5),
        $star(1480, 420, 8, 3.1),
        $star(220, 420, 8, 3.1),
        $star(1300, 140, 7, 2.7),
    ];

    // The whole building complex is authored once at full size, then fitted
    // into the gap between the monument and the seal's footprint (which sits
    // centre-right in the foreground) via a single pivot-on-the-ground-line
    // transform, rather than a hero-specific redraw.
    $domeX = 733;
    $domeY = 449;
    $buildingTransform = 'translate(733 '.$groundY.') scale(.78) translate(-1050 -'.$groundY.')';

    // Sunburst rays from the dome's crown.
    $rays = [];
    for ($i = 0; $i < 28; $i++) {
        $a = $i * (2 * M_PI / 28);
        $r1 = 56;
        $r2 = $i % 2 === 0 ? 133 : 101;
        $rays[] = [
            round($domeX + $r1 * cos($a), 1), round($domeY + $r1 * sin($a), 1),
            round($domeX + $r2 * cos($a), 1), round($domeY + $r2 * sin($a), 1),
        ];
    }

    // Colonnade columns across the Capitol's central block.
    $columns = [];
    for ($x = 992; $x <= 1108; $x += 14.5) {
        $columns[] = round($x, 1);
    }
@endphp

<div {{ $attributes->merge(['class' => 'capitol-vg pointer-events-none absolute inset-0 overflow-hidden']) }} aria-hidden="true">
    <svg class="h-full w-full text-gold" viewBox="0 0 1600 860" preserveAspectRatio="xMidYMid slice" fill="none" xmlns="http://www.w3.org/2000/svg">
        {{-- Stars --}}
        <g fill="currentColor" opacity=".3">
            @foreach ($stars as $d)
                <path d="{{ $d }}" />
            @endforeach
        </g>

        {{-- Sunburst behind the dome --}}
        <g stroke="currentColor" stroke-width="1" opacity=".22">
            @foreach ($rays as [$x1, $y1, $x2, $y2])
                <line x1="{{ $x1 }}" y1="{{ $y1 }}" x2="{{ $x2 }}" y2="{{ $y2 }}" />
            @endforeach
        </g>

        {{-- Reflecting pool axis --}}
        <g stroke="currentColor">
            <line x1="250" y1="{{ $groundY }}" x2="1350" y2="{{ $groundY }}" stroke-width="1.4" opacity=".4" />
            <line x1="460" y1="{{ $groundY + 10 }}" x2="1340" y2="{{ $groundY + 10 }}" stroke-width="1" opacity=".18" stroke-dasharray="1 7" />
        </g>

        {{-- Washington Monument --}}
        <g stroke="currentColor" fill="none" stroke-width="1.6" opacity=".6">
            <path d="M398 {{ $groundY }} L408 360 L432 328 L456 360 L466 {{ $groundY }} Z" />
            <line x1="408" y1="360" x2="456" y2="360" stroke-width="1" opacity=".5" />
        </g>

        {{-- The Capitol: shifted clear of the seal's footprint, see $buildingTransform above --}}
        <g transform="{{ $buildingTransform }}">
            <g stroke="currentColor" fill="none" stroke-width="1.6" opacity=".65">
                {{-- wings --}}
                <path d="M780 {{ $groundY }} L780 575 L985 575 L985 {{ $groundY }}" />
                <path d="M1115 {{ $groundY }} L1115 575 L1320 575 L1320 {{ $groundY }}" />
                <line x1="780" y1="575" x2="985" y2="575" stroke-width="1" opacity=".5" />
                <line x1="1115" y1="575" x2="1320" y2="575" stroke-width="1" opacity=".5" />

                {{-- central block + colonnade --}}
                <path d="M978 575 L978 520 L1122 520 L1122 575" />
                <g stroke-width="1" opacity=".55">
                    @foreach ($columns as $cx)
                        <line x1="{{ $cx }}" y1="528" x2="{{ $cx }}" y2="572" />
                    @endforeach
                </g>
                <line x1="978" y1="520" x2="1122" y2="520" stroke-width="1.2" opacity=".6" />

                {{-- drum + dome --}}
                <path d="M1006 520 L1006 466 L1094 466 L1094 520" />
                <path d="M1002 466 A48 48 0 0 1 1098 466" />

                {{-- lantern + spire --}}
                <rect x="1034" y="432" width="32" height="34" />
                <path d="M1034 432 A16 16 0 0 1 1066 432" />
                <line x1="1050" y1="416" x2="1050" y2="388" stroke-width="1.2" />
                <circle cx="1050" cy="384" r="3" fill="currentColor" stroke="none" />
            </g>

            {{-- Slow pulse from the dome, echoing the network hubs --}}
            <circle class="net-pulse" cx="1050" cy="384" r="8" stroke="currentColor" stroke-width="1.4" fill="none" opacity=".7">
                <animate attributeName="r" values="8;54" dur="4.2s" repeatCount="indefinite" />
                <animate attributeName="opacity" values=".7;0" dur="4.2s" repeatCount="indefinite" />
            </circle>
        </g>
    </svg>
</div>
