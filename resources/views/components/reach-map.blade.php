@php
    // A deterministic, non-random dot texture standing in for a literal map —
    // no external image, just a loose "global network" mood.
    $reachDots = [];
    for ($row = 0; $row < 9; $row++) {
        for ($col = 0; $col < 26; $col++) {
            $noise = sin($row * 12.9898 + $col * 78.233) * 43758.5453;
            $fraction = $noise - floor($noise);

            if ($fraction > 0.58) {
                $reachDots[] = [
                    'cx' => $col * 16 + 8,
                    'cy' => $row * 16 + 8,
                    'r' => $fraction > 0.82 ? 2.4 : 1.3,
                ];
            }
        }
    }
@endphp

<svg viewBox="0 0 424 152" {{ $attributes->merge(['class' => 'h-auto w-full max-w-md text-gold/50']) }} aria-hidden="true">
    @foreach ($reachDots as $dot)
        <circle cx="{{ $dot['cx'] }}" cy="{{ $dot['cy'] }}" r="{{ $dot['r'] }}" fill="currentColor" />
    @endforeach
</svg>
