@props([
    'name',
    'label',
    'checked' => false,
])

<div class="flex items-center gap-2.5">
    <input
        type="checkbox"
        name="{{ $name }}"
        id="{{ $name }}"
        value="1"
        @checked(old($name, $checked))
        {{ $attributes->merge(['class' => 'h-4 w-4 border border-border bg-surface text-gold focus:outline-none']) }}
    >
    <label for="{{ $name }}" class="text-xs font-semibold uppercase tracking-wider text-muted">{{ $label }}</label>
    @error($name)
        <p class="text-xs text-danger">{{ $message }}</p>
    @enderror
</div>
