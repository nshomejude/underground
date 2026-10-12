@props(['capability' => null, 'icons' => []])

@php
    $isEdit = $capability !== null;
    $labelClass = 'text-[13px] font-medium text-body';
    $inputClass = 'w-full rounded-adm border border-hairline bg-surface px-3 py-2 text-sm text-cream placeholder:text-muted focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/25';
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
    <input type="checkbox" id="is_featured" name="is_featured" value="1" @checked(old('is_featured', $capability->isFeatured ?? false)) class="h-4 w-4 rounded-[3px] border-hairline bg-ink accent-gold">
    Feature on the mobile capability summary
</label>

<div class="flex flex-col gap-2 border-t border-hairline pt-6">
    <label for="problem_statement" class="{{ $labelClass }}">Problem statement</label>
    <p class="text-xs text-muted">The problem this expertise addresses, shown near the top of the detail page.</p>
    <textarea id="problem_statement" name="problem_statement" rows="3" class="{{ $inputClass }}">{{ old('problem_statement', $capability->problemStatement ?? '') }}</textarea>
    @error('problem_statement')<p class="{{ $errorClass }}">{{ $message }}</p>@enderror
</div>

<div class="flex flex-col gap-2">
    <label for="audiences" class="{{ $labelClass }}">Audiences</label>
    <p class="text-xs text-muted">Who this serves — one audience per line.</p>
    <textarea id="audiences" name="audiences" rows="4" class="{{ $inputClass }}">{{ old('audiences', isset($capability) ? implode("\n", $capability->audiences) : '') }}</textarea>
    @error('audiences')<p class="{{ $errorClass }}">{{ $message }}</p>@enderror
</div>

<div class="flex flex-col gap-2">
    <label for="method" class="{{ $labelClass }}">Method</label>
    <p class="text-xs text-muted">How Underground approaches it.</p>
    <textarea id="method" name="method" rows="4" class="{{ $inputClass }}">{{ old('method', $capability->method ?? '') }}</textarea>
    @error('method')<p class="{{ $errorClass }}">{{ $message }}</p>@enderror
</div>

<div class="flex flex-col gap-2">
    <label for="deliverables" class="{{ $labelClass }}">Deliverables</label>
    <p class="text-xs text-muted">Concrete outputs per mission — one deliverable per line.</p>
    <textarea id="deliverables" name="deliverables" rows="4" class="{{ $inputClass }}">{{ old('deliverables', isset($capability) ? implode("\n", $capability->deliverables) : '') }}</textarea>
    @error('deliverables')<p class="{{ $errorClass }}">{{ $message }}</p>@enderror
</div>

<div class="flex flex-col gap-2">
    <label for="confirmed_lead" class="{{ $labelClass }}">Confirmed lead (optional)</label>
    <p class="text-xs text-muted">Only name a partner here if their lead on this practice is genuinely confirmed. Leave blank otherwise — the detail page omits the section rather than guessing.</p>
    <input type="text" id="confirmed_lead" name="confirmed_lead" value="{{ old('confirmed_lead', $capability->confirmedLead ?? '') }}" class="{{ $inputClass }}">
    @error('confirmed_lead')<p class="{{ $errorClass }}">{{ $message }}</p>@enderror
</div>

<div class="flex flex-col gap-2">
    <label for="authorized_reference" class="{{ $labelClass }}">Authorized reference (optional)</label>
    <p class="text-xs text-muted">Only point to a case study or reference that genuinely exists elsewhere on this site. Leave blank otherwise.</p>
    <input type="text" id="authorized_reference" name="authorized_reference" value="{{ old('authorized_reference', $capability->authorizedReference ?? '') }}" class="{{ $inputClass }}">
    @error('authorized_reference')<p class="{{ $errorClass }}">{{ $message }}</p>@enderror
</div>

<div class="flex flex-col gap-2">
    <label for="next_action" class="{{ $labelClass }}">Next action</label>
    <p class="text-xs text-muted">The call-to-action line shown at the end of the detail page, e.g. "Start a confidential inquiry".</p>
    <input type="text" id="next_action" name="next_action" value="{{ old('next_action', $capability->nextAction ?? 'Start a confidential inquiry') }}" class="{{ $inputClass }}">
    @error('next_action')<p class="{{ $errorClass }}">{{ $message }}</p>@enderror
</div>
