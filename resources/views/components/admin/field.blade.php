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
    <label for="{{ $inputId }}" class="text-[13px] font-medium text-body">{{ $label }}</label>
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $inputId }}"
        value="{{ old($errorKey, $value) }}"
        @if ($required) required @endif
        {{ $attributes->merge(['class' => 'rounded-adm border border-hairline bg-surface px-3 py-2 text-sm text-cream placeholder:text-muted focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/25']) }}
    >
    @error($errorKey)
        <p class="text-xs text-danger">{{ $message }}</p>
    @enderror
</div>
