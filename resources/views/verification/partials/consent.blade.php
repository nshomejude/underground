@props(['kind', 'action'])
@php $days = (int) config('verification.retention_days'); $window = config('verification.review_window'); @endphp
<form method="POST" action="{{ $action }}" class="ac-panel ac-stack" novalidate>
    @csrf
    <div class="ac-ph"><x-icon name="lock" class="ac-pi" /><h2 class="ac-h3">Your privacy, in plain language</h2></div>
    <ul class="vf-facts">
        <li><x-icon name="file-text" /><p><strong>What we collect.</strong>
            @if ($kind === 'identity')
                Photos or scans of your ID card, passport or licence, optionally a selfie holding it, your name and date of birth as shown, the document's expiry date and only the last 4 characters of its number. We never store the full number.
            @else
                Your company's registration details and the official documents you upload (registration certificate, proof of address and any optional ones).
            @endif
        </p></li>
        <li><x-icon name="shield-check" /><p><strong>Why.</strong> To confirm that
            {{ $kind === 'identity' ? 'you are the person you say you are' : 'your company exists and you act for it' }}
            before the network lists you and lets you connect, message and vote. A person reads your documents: there is no automatic face matching.</p></li>
        <li><x-icon name="eye" /><p><strong>Who sees it.</strong> Only authorised members of our review team. Files are kept in private storage, never on public pages, and are never shown to other members. Other members only see a verified badge.</p></li>
        <li><x-icon name="clock" /><p><strong>How long.</strong> We review within {{ $window }}.
            Files from rejected, expired or withdrawn submissions are deleted after {{ $days }} days.
            @if (config('verification.strip_approved_files'))
                Document images are removed shortly after approval.
            @else
                Approved documents are kept while your verification is valid, then deleted.
            @endif
        </p></li>
        <li><x-icon name="trash-2" /><p><strong>Your rights.</strong> You can withdraw at any time. Withdrawing deletes your files and the date of birth you entered immediately.</p></li>
    </ul>
    <label class="vf-check">
        <input type="checkbox" name="consent" value="1" required @checked(old('consent'))
            @error('consent') aria-invalid="true" aria-describedby="consent-error" @enderror>
        <span>I have read this and consent to {{ config('app.name') }} processing my {{ $kind === 'identity' ? 'identity documents' : 'company documents' }} for verification. <span class="vf-help">(Consent version {{ config('verification.consent_version') }})</span></span>
    </label>
    @error('consent')<p id="consent-error" class="ac-err">{{ $message }}</p>@enderror
    <div class="vf-actions">
        <a class="ac-btn" href="{{ route('verification.index') }}">Back</a>
        <button class="ac-btn ac-btn-solid" type="submit">Agree and continue <x-icon name="arrow-right" class="h-4 w-4" /></button>
    </div>
</form>
