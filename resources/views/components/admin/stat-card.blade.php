@props([
    'label',
    'value',
    'icon',
    'tone' => 'gold',
])

@php
    // Tailwind's scanner needs full literal class strings, not runtime
    // concatenation — so the tone maps to a complete pair rather than
    // interpolating "bg-{$tone}/10" and "text-{$tone}" directly.
    $toneClasses = match ($tone) {
        'success' => 'bg-success/10 text-success',
        'warning' => 'bg-warning/10 text-warning',
        'danger' => 'bg-danger/10 text-danger',
        'info' => 'bg-info/10 text-info',
        default => 'bg-gold/10 text-gold',
    };
@endphp

<div class="flex items-center gap-3.5 rounded-adm border border-hairline bg-surface p-4 shadow-adm-xs">
    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-adm {{ $toneClasses }}">
        <x-icon :name="$icon" class="h-4 w-4" />
    </span>
    <div class="flex min-w-0 flex-col">
        <span class="text-xl font-semibold tabular-nums leading-tight text-cream">{{ $value }}</span>
        <span class="truncate text-[12px] text-muted">{{ $label }}</span>
    </div>
</div>
