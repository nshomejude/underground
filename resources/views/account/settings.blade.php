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

        <div class="ac-panel ac-stack">
            <div class="ac-ph">
                <x-icon name="shield-check" class="ac-pi" />
                <h3 class="ac-h3">Password &amp; security</h3>
            </div>
            <p class="ac-lead">Change your password, check your email verification and manage your account on the Security page.</p>
            <a href="{{ route('account.security') }}" class="ac-btn ac-fit">
                Open Security
                <x-icon name="chevron-right" class="ac-bi" />
            </a>
        </div>

        <a href="{{ route('account.show') }}" class="ac-link ac-back">
            <x-icon name="chevron-right" class="ac-bi ac-flip" />
            Back to My Account
        </a>

    </div>
</x-account.shell>
