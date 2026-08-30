@props([
    'name',
    'label',
    'options' => [],
    'value' => null,
    'required' => true,
])

<div class="flex flex-col gap-1.5">
    <label for="{{ $name }}" class="text-[13px] font-medium text-body">{{ $label }}</label>
    <select
        name="{{ $name }}"
        id="{{ $name }}"
        @if ($required) required @endif
        {{ $attributes->merge(['class' => 'rounded-adm border border-hairline bg-surface px-3 py-2 text-sm text-cream focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/25']) }}
    >
        @unless ($required)
            <option value="">&mdash; None &mdash;</option>
        @endunless
        @foreach ($options as $option)
            <option value="{{ $option }}" @selected(old($name, $value) === $option)>{{ $option }}</option>
        @endforeach
    </select>
    @error($name)
        <p class="text-xs text-danger">{{ $message }}</p>
    @enderror
</div>
