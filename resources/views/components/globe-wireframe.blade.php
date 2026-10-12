@php
    /*
     * Decorative wireframe globe: an orthographic sphere of latitude/
     * longitude lines, turning steadily like the hero's other dials and
     * gears, with a few glowing points marking the firm's real offices.
     * Pure inline SVG + CSS rotation (the same gear-rot keyframe the
     * gear background uses) — no JavaScript, no canvas. A lightweight,
     * ambient cousin of the interactive <x-globe-atlas /> used on the
     * Global Reach page.
     */
    $cx = 800;
    $cy = 430;
    $r = 300;

    // Meridians (lines of longitude), evenly spaced across the visible face.
    $meridians = [];
    for ($i = -4; $i <= 4; $i++) {
        $lon = $i * 20; // degrees from centre
        $meridians[] = round($r * cos(deg2rad($lon)), 1);
    }

    // Parallels (lines of latitude), flattened for a sphere-like read.
    $parallels = [];
    foreach ([-60, -30, 0, 30, 60] as $lat) {
        $parallels[] = [
            'y' => round($cy - $r * sin(deg2rad($lat)), 1),
            'rx' => round($r * cos(deg2rad($lat)), 1),
            'ry' => round($r * 0.14, 1),
        ];
    }

    // A handful of office points, placed by eye at roughly their real
    // longitude/latitude relative to the globe's centre meridian.
    $offices = [
        ['label' => 'Washington, D.C.', 'lon' => -40, 'lat' => 22],
        ['label' => 'Paris', 'lon' => 6, 'lat' => 30],
        ['label' => 'Abidjan', 'lon' => -2, 'lat' => -2],
        ['label' => 'Lagos', 'lon' => 12, 'lat' => -4],
        ['label' => 'Douala', 'lon' => 18, 'lat' => -12],
    ];
    $points = [];
    foreach ($offices as $o) {
        $points[] = [
            'x' => round($cx + $r * cos(deg2rad($o['lat'])) * sin(deg2rad($o['lon'])), 1),
            'y' => round($cy - $r * sin(deg2rad($o['lat'])), 1),
        ];
    }
@endphp

<div {{ $attributes->merge(['class' => 'globe-wire pointer-events-none absolute inset-0 overflow-hidden']) }} aria-hidden="true">
    <svg class="h-full w-full text-gold" viewBox="0 0 1600 860" preserveAspectRatio="xMidYMid slice" fill="none" xmlns="http://www.w3.org/2000/svg">
        <g class="globe-spin" style="transform-origin: {{ $cx }}px {{ $cy }}px; --d: 22s" stroke="currentColor">
            <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}" stroke-width="1.6" opacity=".55" />

            @foreach ($meridians as $rx)
                <ellipse cx="{{ $cx }}" cy="{{ $cy }}" rx="{{ abs($rx) }}" ry="{{ $r }}" stroke-width="1" opacity=".3" />
            @endforeach

            @foreach ($parallels as $p)
                <ellipse cx="{{ $cx }}" cy="{{ $p['y'] }}" rx="{{ $p['rx'] }}" ry="{{ $p['ry'] }}" stroke-width="1" opacity=".3" />
            @endforeach

            @foreach ($points as $k => $pt)
                <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="4.5" fill="currentColor" stroke="none" opacity=".9" />
                <circle class="net-pulse" cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="6" stroke="currentColor" stroke-width="1.4" fill="none">
                    <animate attributeName="r" values="6;28" dur="3.2s" begin="{{ -($k * 0.8) }}s" repeatCount="indefinite" />
                    <animate attributeName="opacity" values=".8;0" dur="3.2s" begin="{{ -($k * 0.8) }}s" repeatCount="indefinite" />
                </circle>
            @endforeach
        </g>
    </svg>
</div>
