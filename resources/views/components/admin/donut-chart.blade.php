@props([
    'segments', // list<array{label: string, value: int, color: string}> — color is a --color-* token name
    'centerLabel' => null,
])

@php
    $total = array_sum(array_column($segments, 'value'));
    $stops = [];
    $cursor = 0;

    foreach ($segments as $segment) {
        if ($segment['value'] <= 0) {
            continue;
        }

        $start = $total > 0 ? $cursor / $total * 100 : 0;
        $cursor += $segment['value'];
        $end = $total > 0 ? $cursor / $total * 100 : 0;

        $stops[] = "var(--color-{$segment['color']}) {$start}% {$end}%";
    }

    $gradient = $total > 0 && $stops !== []
        ? 'conic-gradient('.implode(', ', $stops).')'
        : 'conic-gradient(var(--color-hairline) 0% 100%)';
@endphp

<div class="flex items-center gap-5">
    <div class="relative h-24 w-24 shrink-0 rounded-full" style="background: {{ $gradient }}">
        <div class="absolute inset-[9px] flex flex-col items-center justify-center rounded-full bg-surface">
            <span class="text-lg font-semibold tabular-nums text-cream">{{ $total }}</span>
            @if ($centerLabel)
                <span class="text-[10px] uppercase tracking-wide text-muted">{{ $centerLabel }}</span>
            @endif
        </div>
    </div>

    <ul class="flex flex-1 flex-col gap-1.5">
        @foreach ($segments as $segment)
            <li class="flex items-center justify-between gap-3 text-[13px]">
                <span class="flex items-center gap-2 text-body">
                    <span class="h-2 w-2 shrink-0 rounded-full" style="background: var(--color-{{ $segment['color'] }})"></span>
                    {{ $segment['label'] }}
                </span>
                <span class="font-medium tabular-nums text-cream">{{ $segment['value'] }}</span>
            </li>
        @endforeach
    </ul>
</div>
