@props(['insight' => null])

@php
    $isEdit = $insight !== null;
    $labelClass = 'text-xs font-semibold uppercase tracking-widest text-muted';
    $inputClass = 'w-full border border-border bg-ink px-4 py-3 text-sm text-cream placeholder:text-muted focus:border-gold focus:outline-none';
    $errorClass = 'text-xs text-danger';
@endphp

<div class="flex flex-col gap-2">
    <label for="slug" class="{{ $labelClass }}">Slug</label>
    @if ($isEdit)
        <input type="text" id="slug" value="{{ $insight->slug->value }}" disabled class="{{ $inputClass }} cursor-not-allowed opacity-60">
        <p class="text-xs text-muted">The slug is permanent once an insight is created.</p>
    @else
        <input type="text" id="slug" name="slug" value="{{ old('slug') }}" placeholder="e.g. sahel-strategic-outlook" class="{{ $inputClass }}">
        @error('slug')<p class="{{ $errorClass }}">{{ $message }}</p>@enderror
    @endif
</div>

<div class="flex flex-col gap-2">
    <label for="title" class="{{ $labelClass }}">Title</label>
    <input type="text" id="title" name="title" value="{{ old('title', $insight->title ?? '') }}" class="{{ $inputClass }}">
    @error('title')<p class="{{ $errorClass }}">{{ $message }}</p>@enderror
</div>

<div class="flex flex-col gap-2">
    <label for="category" class="{{ $labelClass }}">Category</label>
    <input type="text" id="category" name="category" value="{{ old('category', $insight->category ?? '') }}" placeholder="e.g. Geopolitics" class="{{ $inputClass }}">
    @error('category')<p class="{{ $errorClass }}">{{ $message }}</p>@enderror
</div>

<div class="flex flex-col gap-2">
    <label for="excerpt" class="{{ $labelClass }}">Excerpt</label>
    <textarea id="excerpt" name="excerpt" rows="3" class="{{ $inputClass }}">{{ old('excerpt', $insight->excerpt ?? '') }}</textarea>
    @error('excerpt')<p class="{{ $errorClass }}">{{ $message }}</p>@enderror
</div>

<div class="flex flex-col gap-2">
    <label for="body" class="{{ $labelClass }}">Body</label>
    <textarea id="body" name="body" rows="12" class="{{ $inputClass }} font-mono">{{ old('body', $insight->body ?? '') }}</textarea>
    @error('body')<p class="{{ $errorClass }}">{{ $message }}</p>@enderror
</div>

<div class="flex flex-col gap-2">
    <label for="published_at" class="{{ $labelClass }}">Published At</label>
    <input
        type="datetime-local"
        id="published_at"
        name="published_at"
        value="{{ old('published_at', $insight?->publishedAt?->format('Y-m-d\TH:i')) }}"
        class="{{ $inputClass }}"
    >
    <p class="text-xs text-muted">Leave blank to keep this insight as an unpublished draft.</p>
    @error('published_at')<p class="{{ $errorClass }}">{{ $message }}</p>@enderror
</div>
