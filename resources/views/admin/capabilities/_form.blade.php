@props(['capability' => null, 'icons' => []])

@php
    $isEdit = $capability !== null;
    $labelClass = 'text-xs font-semibold uppercase tracking-widest text-muted';
    $inputClass = 'w-full border border-border bg-ink px-4 py-3 text-sm text-cream placeholder:text-muted focus:border-gold focus:outline-none';
    $errorClass = 'text-xs text-danger';
    $selectedIcon = old('icon', $capability->icon ?? $icons[0] ?? '');
@endphp

<div class="flex flex-col gap-2">
    <label for="slug" class="{{ $labelClass }}">Slug</label>
    @if ($isEdit)
        <input type="text" id="slug" value="{{ $capability->slug->value }}" disabled class="{{ $inputClass }} cursor-not-allowed opacity-60">
        <p class="text-xs text-muted">The slug is permanent once a capability is created.</p>
    @else
        <input type="text" id="slug" name="slug" value="{{ old('slug') }}" placeholder="e.g. strategic-intelligence-analysis" class="{{ $inputClass }}">
        @error('slug')<p class="{{ $errorClass }}">{{ $message }}</p>@enderror
    @endif
</div>

<div class="flex flex-col gap-2">
    <label for="title" class="{{ $labelClass }}">Title</label>
    <input type="text" id="title" name="title" value="{{ old('title', $capability->title ?? '') }}" class="{{ $inputClass }}">
    @error('title')<p class="{{ $errorClass }}">{{ $message }}</p>@enderror
</div>

<div class="flex flex-col gap-2">
    <label for="summary" class="{{ $labelClass }}">Summary</label>
    <textarea id="summary" name="summary" rows="4" class="{{ $inputClass }}">{{ old('summary', $capability->summary ?? '') }}</textarea>
    @error('summary')<p class="{{ $errorClass }}">{{ $message }}</p>@enderror
</div>

<div class="flex flex-col gap-2">
    <label for="icon" class="{{ $labelClass }}">Icon</label>
    <select id="icon" name="icon" class="{{ $inputClass }}">
        @foreach ($icons as $icon)
            <option value="{{ $icon }}" @selected($selectedIcon === $icon)>{{ $icon }}</option>
        @endforeach
    </select>
    @error('icon')<p class="{{ $errorClass }}">{{ $message }}</p>@enderror
</div>

<div class="flex flex-col gap-2">
    <label for="position" class="{{ $labelClass }}">Position</label>
    <input type="number" min="0" id="position" name="position" value="{{ old('position', $capability->position ?? 0) }}" class="{{ $inputClass }}">
    <p class="text-xs text-muted">Lower numbers appear first.</p>
    @error('position')<p class="{{ $errorClass }}">{{ $message }}</p>@enderror
</div>

<label class="flex items-center gap-3 text-sm text-body">
    <input type="checkbox" id="is_featured" name="is_featured" value="1" @checked(old('is_featured', $capability->isFeatured ?? false)) class="h-4 w-4 border-border bg-ink accent-gold">
    Feature on the mobile capability summary
</label>
