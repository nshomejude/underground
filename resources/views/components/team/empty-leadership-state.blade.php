@props([
    'icon' => 'users',
    'text',
])

<div class="flex flex-col items-center gap-4 border border-dashed border-border px-8 py-16 text-center">
    <span class="flex h-14 w-14 items-center justify-center rounded-full border border-border text-muted">
        <x-icon :name="$icon" class="h-6 w-6" />
    </span>
    <p class="max-w-md text-sm leading-relaxed text-muted">{{ $text }}</p>
</div>
