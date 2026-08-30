@props([
    'label',
    'tone' => 'neutral',
])

@php
    $tones = [
        'success' => 'border-success/40 bg-success/10 text-success',
        'warning' => 'border-warning/40 bg-warning/10 text-warning',
        'danger' => 'border-danger/40 bg-danger/10 text-danger',
        'info' => 'border-info/40 bg-info/10 text-info',
        'neutral' => 'border-border bg-surface text-muted',
    ];

    $classes = $tones[$tone] ?? $tones['neutral'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-[11px] font-semibold uppercase tracking-wider {$classes}"]) }}>
    <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-current"></span>
    {{ $label }}
</span>
