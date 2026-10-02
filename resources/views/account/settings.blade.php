<x-account.shell title="Settings" active="settings">
    <header class="ac-top"><div><p class="ac-eyebrow">Member Account</p><h1>Settings</h1></div></header>
    <div class="ac-narrow">

        @if (session('status'))
            <p class="ac-flash ac-flash-ok" role="status">
                {{ session('status') }}
            </p>
        @endif

        <div class="ac-panel ac-stack">
            <div class="ac-ph">
                <x-icon name="user" class="ac-pi" />
                <h3 class="ac-h3">Profile</h3>
            </div>

            <p class="ac-lead">
                Changing your email requires your current password, since it is the address used to recover
                your account.
            </p>

            <form method="POST" action="{{ route('account.settings.update') }}" novalidate class="ac-stack">
                @csrf

                <div class="ac-field">
                    <label for="name" class="ac-label">
                        Full Name <span class="text-gold" aria-hidden="true">*</span>
                        <span class="sr-only">required</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        required
                        aria-required="true"
                        @if ($errors->updateProfile->has('name')) aria-invalid="true" aria-describedby="name-error" @endif
                        class="ac-input"
                    >
                    @error('name', 'updateProfile')
                        <p id="name-error" class="ac-err">{{ $message }}</p>
                    @enderror
                </div>

                <div class="ac-field">
                    <label for="email" class="ac-label">
                        Email <span class="text-gold" aria-hidden="true">*</span>
                        <span class="sr-only">required</span>
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        required
                        aria-required="true"
                        @if ($errors->updateProfile->has('email')) aria-invalid="true" aria-describedby="email-error" @endif
                        class="ac-input"
                    >
                    @error('email', 'updateProfile')
                        <p id="email-error" class="ac-err">{{ $message }}</p>
                    @enderror
                </div>

                <div class="ac-field">
                    <label for="current_password" class="ac-label">
                        Current Password
                        <span class="ac-hint">(only required if changing email)</span>
                    </label>
                    <input
                        type="password"
                        id="current_password"
                        name="current_password"
                        autocomplete="current-password"
                        @if ($errors->updateProfile->has('current_password')) aria-invalid="true" aria-describedby="current_password-error" @endif
                        class="ac-input"
                    >
                    @error('current_password', 'updateProfile')
                        <p id="current_password-error" class="ac-err">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="ac-btn ac-btn-solid ac-fit">
                    Save Profile
                    <x-icon name="check-circle" class="ac-bi" />
                </button>
            </form>
        </div>

        <div class="ac-panel ac-stack" id="security">
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

        <a href="{{ route('account.show') }}" class="ac-link ac-back">
            <x-icon name="chevron-right" class="ac-bi ac-flip" />
            Back to My Account
        </a>

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
