@php
    $hint ??= null; $file ??= null; $allowPdf ??= true; $required ??= false; $optionalTag ??= null; $maxMb ??= 8; $camera ??= 'environment';
@endphp
@php
    $accept = $allowPdf ? 'image/jpeg,image/png,image/webp,application/pdf' : 'image/jpeg,image/png,image/webp';
    $fileUrl = $file ? route('verification.file', [$kind, $rowId, $name]) : null;
    $isImage = $file && str_starts_with($file['mime'] ?? '', 'image/');
@endphp
<div class="vf-drop {{ $file ? 'is-has' : '' }} @error($name) is-bad @enderror" data-drop data-max-mb="{{ $maxMb }}" data-min="{{ config('verification.min_image_dimension') }}" data-pdf="{{ $allowPdf ? 1 : 0 }}">
    <div class="vf-drop-head">
        <label for="f-{{ $name }}">{{ $label }}@if ($required) <span class="sr-only">(required)</span>@endif</label>
        <span class="vf-tag">{{ $required ? 'Required' : ($optionalTag ?? 'Optional') }}</span>
    </div>
    @if ($hint)<p class="vf-help" id="h-{{ $name }}">{{ $hint }}</p>@endif
    <div class="vf-drop-body">
        <div class="vf-thumb" data-thumb>
            @if ($isImage)<img src="{{ $fileUrl }}" alt="Uploaded {{ strtolower($label) }}">
            @elseif ($file)<span>PDF</span>
            @else<x-icon name="file-up" class="h-6 w-6" />@endif
        </div>
        <div class="vf-pick">
            <input type="file" id="f-{{ $name }}" name="{{ $name }}" accept="{{ $accept }}" aria-describedby="h-{{ $name }} e-{{ $name }}" data-input>
            <label class="ac-btn vf-camera" for="c-{{ $name }}"><x-icon name="camera" class="h-4 w-4" />Take a photo</label>
            <input type="file" id="c-{{ $name }}" class="sr-only" accept="image/*" capture="{{ $camera }}" data-camera tabindex="-1">
        </div>
    </div>
    <p class="vf-help" data-status aria-live="polite">
        @if ($file) {{ $file['name'] }} saved ({{ number_format(($file['size'] ?? 0) / 1048576, 1) }} MB). Choose another file to replace it. @else JPG, PNG, WebP{{ $allowPdf ? ' or PDF' : '' }}, up to {{ $maxMb }} MB. @endif
    </p>
    <p class="ac-err" id="e-{{ $name }}" data-error role="alert">@error($name){{ $message }}@enderror</p>
    @if ($file)
        <button type="submit" form="rm-{{ $name }}" class="ac-btn ac-btn-danger"><x-icon name="trash-2" class="h-4 w-4" />Remove this file</button>
    @endif
</div>
