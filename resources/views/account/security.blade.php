<x-account.shell title="Security" active="security">
    <header class="ac-top"><div><p class="ac-eyebrow">Member Account</p><h1>Security</h1></div></header>
    <div class="ac-narrow">

        @if (session('status'))
            <p class="ac-flash ac-flash-ok" role="status">
                {{ session('status') === 'verification-link-sent' ? 'A new verification link has been sent to '.auth()->user()->email.'.' : session('status') }}
            </p>
        @endif

        <div class="ac-panel ac-stack">
            <div class="ac-ph">
                <x-icon name="fingerprint" class="ac-pi" />
                <h3 class="ac-h3">Sign-in &amp; recovery</h3>
            </div>

            <ul class="ac-list">
                <li class="ac-row">
                    <span>Email address</span>
                    <b>{{ auth()->user()->email }}</b>
                </li>
                <li class="ac-row">
                    <span>Email verification</span>
                    @if (auth()->user()->hasVerifiedEmail())
                        <span class="ac-ok"><x-icon name="check" class="ac-bi" /> Verified</span>
                    @else
                        <span class="ac-note">Not verified</span>
                    @endif
                </li>
            </ul>

            @unless (auth()->user()->hasVerifiedEmail())
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="ac-btn ac-fit">
                        Resend Verification Email
                        <x-icon name="rotate-cw" class="ac-bi" />
                    </button>
                </form>
            @endunless

            <p class="ac-hint">We email you whenever your password changes, so you can act at once if it was not you.</p>
        </div>

        <div class="ac-panel ac-stack">
            <div class="ac-ph">
                <x-icon name="lock" class="ac-pi" />
                <h3 class="ac-h3">Password</h3>
            </div>

            <p class="ac-lead">
                You will stay signed in on this device after changing your password.
            </p>

            <form method="POST" action="{{ route('account.settings.password') }}" novalidate class="ac-stack">
                @csrf

                <div class="ac-field">
                    <label for="password_current_password" class="ac-label">
                        Current Password <span class="text-gold" aria-hidden="true">*</span>
                        <span class="sr-only">required</span>
                    </label>
                    <input
                        type="password"
                        id="password_current_password"
                        name="current_password"
                        required
                        aria-required="true"
                        autocomplete="current-password"
                        @if ($errors->updatePassword->has('current_password')) aria-invalid="true" aria-describedby="password_current_password-error" @endif
                        class="ac-input"
                    >
                    @error('current_password', 'updatePassword')
                        <p id="password_current_password-error" class="ac-err">{{ $message }}</p>
                    @enderror
                </div>

                <div class="ac-field">
                    <label for="password" class="ac-label">
                        New Password <span class="text-gold" aria-hidden="true">*</span>
                        <span class="sr-only">required</span>
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        aria-required="true"
                        autocomplete="new-password"
                        @if ($errors->updatePassword->has('password')) aria-invalid="true" aria-describedby="password-error" @endif
                        class="ac-input"
                    >
                    @error('password', 'updatePassword')
                        <p id="password-error" class="ac-err">{{ $message }}</p>
                    @enderror
                </div>

                <div class="ac-field">
                    <label for="password_confirmation" class="ac-label">
                        Confirm New Password <span class="text-gold" aria-hidden="true">*</span>
                        <span class="sr-only">required</span>
                    </label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        required
                        aria-required="true"
                        autocomplete="new-password"
                        class="ac-input"
                    >
                </div>

                <button type="submit" class="ac-btn ac-fit">
                    Change Password
                    <x-icon name="lock" class="ac-bi" />
                </button>
            </form>
        </div>

        <div class="ac-panel ac-stack ac-danger">
            <div class="ac-ph">
                <x-icon name="x" class="ac-pi ac-pi-danger" />
                <h3 class="ac-h3">Delete account</h3>
            </div>

            <p class="ac-lead">
                This permanently removes your account and signs you out. Any membership application you submitted is kept on file by the firm for its own records. This cannot be undone.
            </p>

            <form method="POST" action="{{ route('account.destroy') }}" novalidate class="ac-stack" onsubmit="return confirm('Delete your account permanently?');">
                @csrf
                @method('DELETE')

                <div class="ac-field">
                    <label for="delete_current_password" class="ac-label">Confirm with your password</label>
                    <input type="password" id="delete_current_password" name="current_password" required autocomplete="current-password" class="ac-input">
                    @error('current_password', 'deleteAccount')
                        <p class="ac-err">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="ac-btn ac-btn-danger ac-fit">
                    Delete My Account
                </button>
            </form>
        </div>
    </div>
</x-account.shell>
