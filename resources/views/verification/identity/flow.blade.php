@php
    $steps = ['Consent', 'Details', 'Documents', 'Review'];
    $types = config('network.identity_document_types');
@endphp
<x-account.shell title="Identity verification" active="verification">
    <header class="ac-top">
        <div>
            <p class="ac-eyebrow">Verification</p>
            <h1>Verify your identity</h1>
            <p class="ac-sub">Four short steps. You can save and return at any time.</p>
        </div>
    </header>
    <div class="vf-wrap">
        @if (session('status'))<p class="ac-flash ac-flash-ok" role="status">{{ session('status') }}</p>@endif
        @include('verification.partials.stepper', ['steps' => $steps, 'current' => $step, 'route' => 'verification.identity.show'])

        @if ($step === 1)
            @include('verification.partials.consent', ['kind' => 'identity', 'action' => route('verification.identity.consent')])

        @elseif ($step === 2)
            <form method="POST" action="{{ route('verification.identity.details') }}" class="ac-panel ac-stack" novalidate>
                @csrf
                <div class="ac-ph"><x-icon name="id-card" class="ac-pi" /><h2 class="ac-h3">Your document</h2></div>
                <p class="vf-help">Enter the details exactly as shown on the document. We store only the last characters of the number.</p>
                <div class="vf-form-grid">
                    <div class="ac-field">
                        <label class="ac-label" for="document_type">Document type</label>
                        <select class="ac-input" id="document_type" name="document_type" required>
                            <option value="">Choose...</option>
                            @foreach ($types as $k => $label)<option value="{{ $k }}" @selected(old('document_type', $row->document_type) === $k)>{{ $label }}</option>@endforeach
                        </select>
                        @error('document_type')<p class="ac-err">{{ $message }}</p>@enderror
                    </div>
                    <div class="ac-field">
                        <label class="ac-label" for="document_country">Issuing country</label>
                        <input class="ac-input" id="document_country" name="document_country" required maxlength="80" value="{{ old('document_country', $row->document_country) }}">
                        @error('document_country')<p class="ac-err">{{ $message }}</p>@enderror
                    </div>
                    <div class="ac-field is-wide">
                        <label class="ac-label" for="full_name_on_document">Full name on document</label>
                        <input class="ac-input" id="full_name_on_document" name="full_name_on_document" required maxlength="120" autocomplete="name" value="{{ old('full_name_on_document', $row->full_name_on_document ?: auth()->user()->name) }}">
                        @error('full_name_on_document')<p class="ac-err">{{ $message }}</p>@enderror
                    </div>
                    <div class="ac-field">
                        <label class="ac-label" for="date_of_birth">Date of birth</label>
                        <input class="ac-input" type="date" id="date_of_birth" name="date_of_birth" required value="{{ old('date_of_birth', $dob) }}">
                        @error('date_of_birth')<p class="ac-err">{{ $message }}</p>@enderror
                    </div>
                    <div class="ac-field">
                        <label class="ac-label" for="document_expiry">Document expiry date</label>
                        <input class="ac-input" type="date" id="document_expiry" name="document_expiry" required value="{{ old('document_expiry', $row->document_expiry?->format('Y-m-d')) }}">
                        @error('document_expiry')<p class="ac-err">{{ $message }}</p>@enderror
                    </div>
                    <div class="ac-field">
                        <label class="ac-label" for="document_number_last4">Last characters of the number</label>
                        <input class="ac-input" id="document_number_last4" name="document_number_last4" required maxlength="8" autocomplete="off" value="{{ old('document_number_last4', $row->document_number_last4) }}">
                        <span class="vf-help">The last 4 characters are enough.</span>
                        @error('document_number_last4')<p class="ac-err">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="vf-actions">
                    <a class="ac-btn" href="{{ route('verification.identity.show', ['step' => 1]) }}">Back</a>
                    <div class="grp">
                        <button class="ac-btn" type="submit" name="save_exit" value="1" formnovalidate>Save and exit</button>
                        <button class="ac-btn ac-btn-solid" type="submit">Continue <x-icon name="arrow-right" class="h-4 w-4" /></button>
                    </div>
                </div>
            </form>

        @elseif ($step === 3)
            <form method="POST" action="{{ route('verification.identity.files') }}" enctype="multipart/form-data" class="ac-panel ac-stack" novalidate>
                @csrf
                <div class="ac-ph"><x-icon name="file-up" class="ac-pi" /><h2 class="ac-h3">Upload your documents</h2></div>
                <p class="vf-help">Use a clear, well-lit photo or scan with all four corners visible and no glare. Files go to private storage.</p>
                @include('verification.partials.dropzone', ['name' => 'front', 'label' => 'Document front', 'hint' => 'The page or side with your photo.', 'file' => $files['front'] ?? null, 'kind' => 'identity', 'rowId' => $row->id, 'allowPdf' => true, 'required' => true, 'maxMb' => 8])
                @if (in_array('back', $slots, true))
                    @include('verification.partials.dropzone', ['name' => 'back', 'label' => 'Document back', 'file' => $files['back'] ?? null, 'kind' => 'identity', 'rowId' => $row->id, 'allowPdf' => true, 'required' => true, 'maxMb' => 8])
                @endif
                @include('verification.partials.dropzone', ['name' => 'selfie', 'label' => 'Selfie holding the document', 'hint' => 'Optional. It helps the reviewer; no face matching is done.', 'file' => $files['selfie'] ?? null, 'kind' => 'identity', 'rowId' => $row->id, 'allowPdf' => false, 'required' => false, 'maxMb' => 8, 'camera' => 'user'])
                <div class="vf-actions">
                    <a class="ac-btn" href="{{ route('verification.identity.show', ['step' => 2]) }}">Back</a>
                    <div class="grp">
                        <button class="ac-btn" type="submit" name="save_exit" value="1">Save and exit</button>
                        <button class="ac-btn ac-btn-solid" type="submit">Continue <x-icon name="arrow-right" class="h-4 w-4" /></button>
                    </div>
                </div>
            </form>
            @foreach (['front', 'back', 'selfie'] as $s)
                @if (isset($files[$s]))
                    <form id="rm-{{ $s }}" method="POST" action="{{ route('verification.identity.files.remove', $s) }}">@csrf @method('DELETE')</form>
                @endif
            @endforeach
            @include('verification.partials.dropzone-js')

        @else
            @include('verification.partials.review', ['kind' => 'identity', 'row' => $row, 'checks' => $checks, 'service' => $service, 'files' => $files, 'summary' => [
                'Document' => $types[$row->document_type] ?? $row->document_type,
                'Issuing country' => $row->document_country,
                'Name on document' => $row->full_name_on_document,
                'Date of birth' => $dob,
                'Expiry' => $row->document_expiry?->format('j M Y'),
                'Number (last characters)' => $row->document_number_last4,
            ]])
        @endif
    </div>
</x-account.shell>
