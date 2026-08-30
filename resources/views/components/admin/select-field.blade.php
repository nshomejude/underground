@props([
    'name',
    'label',
    'options' => [],
    'value' => null,
    'required' => true,
])

<div class="flex flex-col gap-1.5">
    <label for="{{ $name }}" class="text-xs font-semibold uppercase tracking-wider text-muted">{{ $label }}</label>
    <select
        name="{{ $name }}"
        id="{{ $name }}"
        @if ($required) required @endif
        {{ $attributes->merge(['class' => 'border border-border bg-surface px-4 py-2.5 text-sm text-cream focus:border-gold focus:outline-none']) }}
    >
        @foreach ($options as $option)
            <option value="{{ $option }}" @selected(old($name, $value) === $option)>{{ $option }}</option>
        @endforeach
    </select>
    @error($name)
        <p class="text-xs text-danger">{{ $message }}</p>
    @enderror
</div>
