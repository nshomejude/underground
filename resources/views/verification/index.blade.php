<x-account.shell title="Verification" active="verification">
    <header class="ac-top">
        <div>
            <p class="ac-eyebrow">Member Account</p>
            <h1>Verification</h1>
            <p class="ac-sub">Prove who you are, and who you act for, to unlock the member network.</p>
        </div>
        <x-verified-badge :user="$user" size="md" />
    </header>

    <div class="vf-wrap">
        @if (session('status'))
            <p class="ac-flash ac-flash-ok" role="status">{{ session('status') }}</p>
        @endif

        @unless ($emailVerified)
            <div class="vf-note vf-note--warn" role="alert">
                <x-icon name="mail" />
                <div>
                    <strong>Verify your email first</strong>
                    We need a confirmed email address before you can start. Check your inbox for our message, or
                    <a class="ac-link" href="{{ route('verification.notice') }}">send it again</a>.
                </div>
            </div>
        @endunless

        <div class="vf-note">
            <x-icon name="shield-check" />
            <div>
                <strong>Reviewed by people, not software</strong>
                Documents are reviewed by our team within {{ config('verification.review_window') }}. We do not use face matching. Only authorised reviewers can see what you upload.
            </div>
        </div>

        <div class="vf-grid">
            @include('verification.partials.card', [
                'kind' => 'identity', 'title' => 'Identity', 'icon' => 'fingerprint', 'row' => $identity,
                'status' => $status['identity'], 'canStart' => $canStartIdentity && $emailVerified,
                'blurb' => 'Confirm your identity with a government-issued ID card or passport.',
                'unlocks' => ['A listing in the member directory', 'Sending and accepting connection requests', 'Messaging your connections', 'Voting on member motions (by tier)'],
            ])
            @include('verification.partials.card', [
                'kind' => 'company', 'title' => 'Company', 'icon' => 'building-2', 'row' => $company,
                'status' => $status['company'], 'canStart' => $canStartCompany && $emailVerified,
                'blurb' => 'Confirm your company with its registration documents.',
                'unlocks' => ['A "Verified company" badge beside your name', 'Greater trust from prospective partners', 'Does not replace identity verification'],
            ])
        </div>
    </div>
</x-account.shell>
