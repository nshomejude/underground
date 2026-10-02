@php
    $steps = ['Consent', 'Company', 'Documents', 'Review'];
    $docTypes = config('network.company_document_types');
    $required = ['registration_certificate', 'proof_of_address'];
    $selSectors = (array) old('sectors', $row->sectors ?? []);
@endphp
<x-account.shell title="Company verification" active="verification">
    <header class="ac-top">
        <div>
            <p class="ac-eyebrow">Verification</p>
            <h1>Verify your company</h1>
            <p class="ac-sub">Four short steps. You can save and return at any time.</p>
        </div>
    </header>
    <div class="vf-wrap">
        @if (session('status'))<p class="ac-flash ac-flash-ok" role="status">{{ session('status') }}</p>@endif
        @include('verification.partials.stepper', ['steps' => $steps, 'current' => $step, 'route' => 'verification.company.show'])

        @if ($step === 1)
            @include('verification.partials.consent', ['kind' => 'company', 'action' => route('verification.company.consent')])

        @elseif ($step === 2)
            <form method="POST" action="{{ route('verification.company.details') }}" class="ac-panel ac-stack" novalidate>
                @csrf
                <div class="ac-ph"><x-icon name="building-2" class="ac-pi" /><h2 class="ac-h3">Company details</h2></div>
                <div class="vf-form-grid">
                    <div class="ac-field">
                        <label class="ac-label" for="company_name">Registered company name</label>
                        <input class="ac-input" id="company_name" name="company_name" required maxlength="160" value="{{ old('company_name', $row->company_name) }}">
                        @error('company_name')<p class="ac-err">{{ $message }}</p>@enderror
                    </div>
                    <div class="ac-field">
                        <label class="ac-label" for="trading_name">Trading name <span class="ac-hint">optional</span></label>
                        <input class="ac-input" id="trading_name" name="trading_name" maxlength="160" value="{{ old('trading_name', $row->trading_name) }}">
                        @error('trading_name')<p class="ac-err">{{ $message }}</p>@enderror
                    </div>
                    <div class="ac-field">
                        <label class="ac-label" for="registration_number">Registration number</label>
                        <input class="ac-input" id="registration_number" name="registration_number" required maxlength="60" value="{{ old('registration_number', $row->registration_number) }}">
                        @error('registration_number')<p class="ac-err">{{ $message }}</p>@enderror
                    </div>
                    <div class="ac-field">
                        <label class="ac-label" for="country">Country of registration</label>
                        <input class="ac-input" id="country" name="country" required maxlength="80" value="{{ old('country', $row->country) }}">
                        @error('country')<p class="ac-err">{{ $message }}</p>@enderror
                    </div>
                    <div class="ac-field">
                        <label class="ac-label" for="incorporation_date">Date of incorporation</label>
                        <input class="ac-input" type="date" id="incorporation_date" name="incorporation_date" required value="{{ old('incorporation_date', $row->incorporation_date?->format('Y-m-d')) }}">
                        @error('incorporation_date')<p class="ac-err">{{ $message }}</p>@enderror
                    </div>
                    <div class="ac-field">
                        <label class="ac-label" for="website">Website <span class="ac-hint">optional</span></label>
                        <input class="ac-input" type="url" id="website" name="website" maxlength="200" placeholder="https://" value="{{ old('website', $row->website) }}">
                        @error('website')<p class="ac-err">{{ $message }}</p>@enderror
                    </div>
                    <div class="ac-field is-wide">
                        <label class="ac-label" for="registered_address">Registered address</label>
                        <textarea class="ac-input" id="registered_address" name="registered_address" rows="3" required maxlength="500">{{ old('registered_address', $row->registered_address) }}</textarea>
                        @error('registered_address')<p class="ac-err">{{ $message }}</p>@enderror
                    </div>
                    <div class="ac-field is-wide">
                        <label class="ac-label" for="applicant_role">Your role at the company</label>
                        <input class="ac-input" id="applicant_role" name="applicant_role" required maxlength="120" placeholder="Director, Founder, Authorised signatory" value="{{ old('applicant_role', $row->applicant_role) }}">
                        @error('applicant_role')<p class="ac-err">{{ $message }}</p>@enderror
                    </div>
                </div>
                <fieldset class="ac-field">
                    <legend class="ac-label">Sectors <span class="ac-hint">up to 6, optional</span></legend>
                    @foreach (config('network.sectors') as $k => $label)
                        <label class="vf-check"><input type="checkbox" name="sectors[]" value="{{ $k }}" @checked(in_array($k, $selSectors, true))><span>{{ $label }}</span></label>
                    @endforeach
                    @error('sectors')<p class="ac-err">{{ $message }}</p>@enderror
                </fieldset>
                <div class="vf-actions">
                    <a class="ac-btn" href="{{ route('verification.company.show', ['step' => 1]) }}">Back</a>
                    <div class="grp">
                        <button class="ac-btn" type="submit" name="save_exit" value="1" formnovalidate>Save and exit</button>
                        <button class="ac-btn ac-btn-solid" type="submit">Continue <x-icon name="arrow-right" class="h-4 w-4" /></button>
                    </div>
                </div>
            </form>

        @elseif ($step === 3)
            <form method="POST" action="{{ route('verification.company.documents') }}" enctype="multipart/form-data" class="ac-panel ac-stack" novalidate>
                @csrf
                <div class="ac-ph"><x-icon name="file-up" class="ac-pi" /><h2 class="ac-h3">Company documents</h2></div>
                <p class="vf-help">The registration certificate and proof of address are required. The others help us approve faster. PDF or image, up to 12 MB each.</p>
                @foreach ($docTypes as $type => $label)
                    @include('verification.partials.dropzone', ['name' => $type, 'label' => $label, 'file' => $files[$type] ?? null, 'kind' => 'company', 'rowId' => $row->id, 'allowPdf' => true, 'required' => in_array($type, $required, true), 'maxMb' => 12])
                @endforeach
                @error('registration_certificate')<p class="ac-err" role="alert">{{ $message }}</p>@enderror
                <div class="vf-actions">
                    <a class="ac-btn" href="{{ route('verification.company.show', ['step' => 2]) }}">Back</a>
                    <div class="grp">
                        <button class="ac-btn" type="submit" name="save_exit" value="1">Save and exit</button>
                        <button class="ac-btn ac-btn-solid" type="submit">Continue <x-icon name="arrow-right" class="h-4 w-4" /></button>
                    </div>
                </div>
            </form>
            @foreach (array_keys($docTypes) as $s)
                @if (isset($files[$s]))
                    <form id="rm-{{ $s }}" method="POST" action="{{ route('verification.company.documents.remove', $s) }}">@csrf @method('DELETE')</form>
                @endif
            @endforeach
            @include('verification.partials.dropzone-js')

        @else
            @include('verification.partials.review', ['kind' => 'company', 'row' => $row, 'checks' => $checks, 'service' => $service, 'files' => $files, 'summary' => [
                'Company' => $row->company_name,
                'Trading name' => $row->trading_name,
                'Registration number' => $row->registration_number,
                'Country' => $row->country,
                'Incorporated' => $row->incorporation_date?->format('j M Y'),
                'Website' => $row->website,
                'Registered address' => $row->registered_address,
                'Your role' => $row->applicant_role,
                'Sectors' => collect($row->sectors ?? [])->map(fn ($s) => config('network.sectors.'.$s, $s))->implode(', '),
            ]])
        @endif
    </div>
</x-account.shell>
