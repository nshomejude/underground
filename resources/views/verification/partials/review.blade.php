@php
    $route = 'verification.'.$kind.'.show';
    $allOk = collect($checks)->every(fn ($c) => $c['ok']);
@endphp
<div class="ac-panel ac-stack">
    <div class="ac-ph"><x-icon name="scan-line" class="ac-pi" /><h2 class="ac-h3">Quality checks</h2></div>
    <p class="vf-help">These automatic checks look at file type, size and resolution only. A person makes the final decision.</p>
    <ul class="vf-checks" aria-label="Quality checks">
        @forelse ($checks as $i => $c)
            <li class="{{ $c['ok'] ? 'ok' : 'bad' }}" style="--i: {{ $i }}">
                <x-icon :name="$c['ok'] ? 'circle-check' : 'circle-x'" />{{ $c['label'] }}<span>{{ $c['detail'] }}</span>
            </li>
        @empty
            <li class="bad"><x-icon name="circle-x" />No files found<span>Go back and upload</span></li>
        @endforelse
    </ul>
</div>

<div class="ac-panel ac-stack">
    <div class="ac-ph"><x-icon name="clipboard-list" class="ac-pi" /><h2 class="ac-h3">Review your details</h2></div>
    <dl class="vf-dl">
        @foreach ($summary as $label => $value)
            <dt>{{ $label }}</dt><dd>{{ $value !== null && $value !== '' ? $value : 'Not provided' }}</dd>
        @endforeach
        @foreach ($files as $slot => $f)
            <dt>{{ $service->slotLabel($slot) }}</dt><dd>{{ $f['name'] ?? 'Uploaded' }}</dd>
        @endforeach
    </dl>
</div>

<form method="POST" action="{{ route('verification.'.$kind.'.submit') }}" class="ac-panel ac-stack" novalidate>
    @csrf
    @error('submission')<p class="ac-err" role="alert">{{ $message }}</p>@enderror
    <label class="vf-check">
        <input type="checkbox" name="checklist" value="1" required @checked(old('checklist'))>
        <span>I confirm the details and documents are genuine, belong to me{{ $kind === 'company' ? ' or my company' : '' }} and are current. I understand a person will review them within {{ config('verification.review_window') }}.</span>
    </label>
    @error('checklist')<p class="ac-err" role="alert">{{ $message }}</p>@enderror
    <div class="vf-actions">
        <a class="ac-btn" href="{{ route($route, ['step' => 3]) }}">Back</a>
        <button class="ac-btn ac-btn-solid" type="submit" @disabled(! $allOk)>Submit for review <x-icon name="send" class="h-4 w-4" /></button>
    </div>
</form>
