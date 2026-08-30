@props([
    'items', // list<array{label: string, count: int, route?: string}>
])

@php
    $max = max(1, ...array_column($items, 'count'));
@endphp

<div class="flex flex-col gap-2.5">
    @foreach ($items as $item)
        @php $width = $item['count'] / $max * 100; @endphp
        @if (isset($item['route']))
            <a href="{{ route($item['route']) }}" class="group grid grid-cols-[7rem_1fr_2.5rem] items-center gap-3 sm:grid-cols-[9rem_1fr_2.5rem]">
                <span class="truncate text-[13px] text-body group-hover:text-cream">{{ $item['label'] }}</span>
                <span class="h-1.5 w-full overflow-hidden rounded-full bg-surface-raised">
                    <span class="block h-full rounded-full bg-gold/70 transition-all group-hover:bg-gold" style="width: {{ $width }}%"></span>
                </span>
                <span class="text-right text-[13px] font-medium tabular-nums text-cream">{{ $item['count'] }}</span>
            </a>
        @else
            <div class="grid grid-cols-[7rem_1fr_2.5rem] items-center gap-3 sm:grid-cols-[9rem_1fr_2.5rem]">
                <span class="truncate text-[13px] text-body">{{ $item['label'] }}</span>
                <span class="h-1.5 w-full overflow-hidden rounded-full bg-surface-raised">
                    <span class="block h-full rounded-full bg-gold/70" style="width: {{ $width }}%"></span>
                </span>
                <span class="text-right text-[13px] font-medium tabular-nums text-cream">{{ $item['count'] }}</span>
            </div>
        @endif
    @endforeach
</div>
