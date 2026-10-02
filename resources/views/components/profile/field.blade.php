@props(['name', 'label', 'value' => null, 'type' => 'text', 'rows' => null, 'hint' => null, 'max' => null, 'options' => null, 'required' => false, 'placeholder' => null, 'autocomplete' => null])

@php
    $key = str_replace(['[', ']'], ['.', ''], $name);
    $id = 'f-'.trim(preg_replace('/[^a-z0-9]+/i', '-', $name), '-');
    $has = $errors->has($key);
    $describe = trim(($has ? $id.'-err ' : '').($hint ? $id.'-hint' : ''));
    $current = old($key, $value);
@endphp

<div class="ac-field pf-field">
    <label for="{{ $id }}" class="ac-label">{{ $label }}@if ($required) <span class="pf-req" aria-hidden="true">*</span><span class="sr-only">required</span>@endif</label>

    @if ($options !== null)
        <select id="{{ $id }}" name="{{ $name }}" class="ac-input" @if ($describe) aria-describedby="{{ $describe }}" @endif @if ($has) aria-invalid="true" @endif>
            <option value="">Select...</option>
            @foreach ($options as $optValue => $optLabel)
                <option value="{{ $optValue }}" @selected((string) $current === (string) $optValue)>{{ $optLabel }}</option>
            @endforeach
        </select>
    @elseif ($rows)
        <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $rows }}" class="ac-input pf-ta" @if ($max) maxlength="{{ $max }}" @endif @if ($placeholder) placeholder="{{ $placeholder }}" @endif @if ($describe) aria-describedby="{{ $describe }}" @endif @if ($has) aria-invalid="true" @endif>{{ $current }}</textarea>
    @else
        <input type="{{ $type }}" id="{{ $id }}" name="{{ $name }}" value="{{ $current }}" class="ac-input" @if ($max) maxlength="{{ $max }}" @endif @if ($placeholder) placeholder="{{ $placeholder }}" @endif @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif @if ($required) required aria-required="true" @endif @if ($describe) aria-describedby="{{ $describe }}" @endif @if ($has) aria-invalid="true" @endif>
    @endif

    @if ($hint)
        <p id="{{ $id }}-hint" class="pf-hint">{{ $hint }}</p>
    @endif
    @error($key)
        <p id="{{ $id }}-err" class="ac-err">{{ $message }}</p>
    @enderror
</div>
