@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'required' => true,
    'errorKey' => null,
])

@php
    // $name is the HTML input name — bracket notation for array fields
    // (e.g. "navigation[0][label]"). old()/$errors use dot notation instead,
    // so a caller wiring up a nested field passes $errorKey explicitly.
    $errorKey ??= $name;
    $inputId = str_replace(['[', ']'], ['-', ''], $name);
@endphp

<div class="flex flex-col gap-1.5">
    <label for="{{ $inputId }}" class="text-xs font-semibold uppercase tracking-wider text-muted">{{ $label }}</label>
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $inputId }}"
        value="{{ old($errorKey, $value) }}"
        @if ($required) required @endif
        {{ $attributes->merge(['class' => 'border border-border bg-surface px-4 py-2.5 text-sm text-cream placeholder:text-muted focus:border-gold focus:outline-none']) }}
    >
    @error($errorKey)
        <p class="text-xs text-danger">{{ $message }}</p>
    @enderror
</div>
