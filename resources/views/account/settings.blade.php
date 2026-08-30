<x-layout title="Account Settings">
    <section class="mx-auto flex max-w-2xl flex-col gap-10 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading eyebrow="Member Account">
            Settings
        </x-section-heading>

        @if (session('status'))
            <p class="border border-success/40 bg-success/10 px-4 py-3 text-sm text-success" role="status">
                {{ session('status') }}
            </p>
        @endif

        <div class="flex flex-col gap-6 border border-border bg-surface px-6 py-8 sm:px-10 sm:py-10">
            <div class="flex items-center gap-3">
                <x-icon name="user" class="h-6 w-6 shrink-0 text-gold" />
                <h3 class="font-serif text-2xl font-semibold text-cream">Profile</h3>
            </div>

            <p class="text-sm leading-relaxed text-body">
                Changing your email requires your current password, since it is the address used to recover
                your account.
            </p>

            <form method="POST" action="{{ route('account.settings.update') }}" novalidate class="flex flex-col gap-6">
                @csrf

                <div class="flex flex-col gap-2">
                    <label for="name" class="text-xs font-semibold uppercase tracking-widest text-body">
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
                        class="border border-border bg-surface px-4 py-3 text-sm text-cream placeholder:text-muted focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold"
                    >
                    @error('name', 'updateProfile')
                        <p id="name-error" class="text-xs text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col gap-2">
                    <label for="email" class="text-xs font-semibold uppercase tracking-widest text-body">
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
                        class="border border-border bg-surface px-4 py-3 text-sm text-cream placeholder:text-muted focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold"
                    >
                    @error('email', 'updateProfile')
                        <p id="email-error" class="text-xs text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col gap-2">
                    <label for="current_password" class="text-xs font-semibold uppercase tracking-widest text-body">
                        Current Password
                        <span class="ml-1 normal-case tracking-normal text-muted">(only required if changing email)</span>
                    </label>
                    <input
                        type="password"
                        id="current_password"
                        name="current_password"
                        autocomplete="current-password"
                        @if ($errors->updateProfile->has('current_password')) aria-invalid="true" aria-describedby="current_password-error" @endif
                        class="border border-border bg-surface px-4 py-3 text-sm text-cream placeholder:text-muted focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold"
                    >
                    @error('current_password', 'updateProfile')
                        <p id="current_password-error" class="text-xs text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <x-button variant="primary" type="submit" class="w-fit">
                    Save Profile
                    <x-icon name="check-circle" class="h-3.5 w-3.5" />
                </x-button>
            </form>
        </div>

        <div class="flex flex-col gap-6 border border-border bg-surface px-6 py-8 sm:px-10 sm:py-10">
            <div class="flex items-center gap-3">
                <x-icon name="lock" class="h-6 w-6 shrink-0 text-gold" />
                <h3 class="font-serif text-2xl font-semibold text-cream">Password</h3>
            </div>

            <p class="text-sm leading-relaxed text-body">
                You will stay signed in on this device after changing your password.
            </p>

            <form method="POST" action="{{ route('account.settings.password') }}" novalidate class="flex flex-col gap-6">
                @csrf

                <div class="flex flex-col gap-2">
                    <label for="password_current_password" class="text-xs font-semibold uppercase tracking-widest text-body">
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
                        class="border border-border bg-surface px-4 py-3 text-sm text-cream placeholder:text-muted focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold"
                    >
                    @error('current_password', 'updatePassword')
                        <p id="password_current_password-error" class="text-xs text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col gap-2">
                    <label for="password" class="text-xs font-semibold uppercase tracking-widest text-body">
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
                        class="border border-border bg-surface px-4 py-3 text-sm text-cream placeholder:text-muted focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold"
                    >
                    @error('password', 'updatePassword')
                        <p id="password-error" class="text-xs text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col gap-2">
                    <label for="password_confirmation" class="text-xs font-semibold uppercase tracking-widest text-body">
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
                        class="border border-border bg-surface px-4 py-3 text-sm text-cream placeholder:text-muted focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold"
                    >
                </div>

                <x-button variant="secondary" type="submit" class="w-fit">
                    Change Password
                    <x-icon name="lock" class="h-3.5 w-3.5" />
                </x-button>
            </form>
        </div>

        <a href="{{ route('account.show') }}" class="inline-flex w-fit items-center gap-2 text-sm text-gold underline decoration-gold/40 underline-offset-4 hover:text-gold-bright">
            <x-icon name="chevron-right" class="h-3.5 w-3.5 rotate-180" />
            Back to My Account
        </a>
    </section>
</x-layout>
