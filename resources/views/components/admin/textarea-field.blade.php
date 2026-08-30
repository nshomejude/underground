@props([
    'name',
    'label',
    'value' => null,
    'required' => true,
    'rows' => 4,
])

<div class="flex flex-col gap-1.5">
    <label for="{{ $name }}" class="text-xs font-semibold uppercase tracking-wider text-muted">{{ $label }}</label>
    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        rows="{{ $rows }}"
        @if ($required) required @endif
        {{ $attributes->merge(['class' => 'border border-border bg-surface px-4 py-2.5 text-sm text-cream placeholder:text-muted focus:border-gold focus:outline-none']) }}
    >{{ old($name, $value) }}</textarea>
    @error($name)
        <p class="text-xs text-danger">{{ $message }}</p>
    @enderror
</div>
