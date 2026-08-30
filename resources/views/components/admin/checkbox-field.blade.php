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
        {{ $attributes->merge(['class' => 'h-4 w-4 rounded-[3px] border border-hairline bg-surface text-gold focus:outline-none focus:ring-2 focus:ring-gold/25']) }}
    >
    <label for="{{ $name }}" class="text-[13px] font-medium text-body">{{ $label }}</label>
    @error($name)
        <p class="text-xs text-danger">{{ $message }}</p>
    @enderror
</div>
