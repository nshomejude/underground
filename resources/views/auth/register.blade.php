<x-layout title="Register">
    <section class="mx-auto flex max-w-md flex-col gap-10 px-4 py-16 sm:px-6 lg:py-24">
        <a href="{{ url('/') }}" class="mx-auto inline-flex">
            <x-brand-mark />
        </a>

        <x-section-heading tag="h1" eyebrow="Member Account">
            Register
        </x-section-heading>

        <p class="text-sm leading-relaxed text-body">
            An account lets you sign in to track a membership application and, once approved,
            carry your permanent membership card. Registering does not itself grant membership
            &mdash; see <a href="{{ route('membership.index') }}" class="text-gold underline decoration-gold/40 underline-offset-4 hover:text-gold-bright">Membership</a>
            to apply for a vetted tier.
        </p>

        <form method="POST" action="{{ route('register') }}" novalidate class="flex flex-col gap-6">
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
                    value="{{ old('name') }}"
                    autocomplete="name"
                    required
                    aria-required="true"
                    autofocus
                    @if ($errors->has('name')) aria-invalid="true" aria-describedby="name-error" @endif
                    class="border border-border bg-surface px-4 py-3 text-sm text-cream placeholder:text-muted focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold"
                >
                @error('name')
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
                    value="{{ old('email') }}"
                    autocomplete="email"
                    required
                    aria-required="true"
                    @if ($errors->has('email')) aria-invalid="true" aria-describedby="email-error" @endif
                    class="border border-border bg-surface px-4 py-3 text-sm text-cream placeholder:text-muted focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold"
                >
                @error('email')
                    <p id="email-error" class="text-xs text-danger">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col gap-2">
                <label for="password" class="text-xs font-semibold uppercase tracking-widest text-body">
                    Password <span class="text-gold" aria-hidden="true">*</span>
                    <span class="sr-only">required</span>
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    autocomplete="new-password"
                    aria-describedby="password-hint"
                    required
                    aria-required="true"
                    @if ($errors->has('password')) aria-invalid="true" aria-describedby="password-error" @endif
                    class="border border-border bg-surface px-4 py-3 text-sm text-cream placeholder:text-muted focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold"
                >
                <p id="password-hint" class="text-xs text-muted">At least 8 characters. A longer passphrase is stronger than a short, complicated one.</p>
                @error('password')
                    <p id="password-error" class="text-xs text-danger">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col gap-2">
                <label for="password_confirmation" class="text-xs font-semibold uppercase tracking-widest text-body">
                    Confirm Password <span class="text-gold" aria-hidden="true">*</span>
                    <span class="sr-only">required</span>
                </label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    autocomplete="new-password"
                    required
                    aria-required="true"
                    class="border border-border bg-surface px-4 py-3 text-sm text-cream placeholder:text-muted focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold"
                >
            </div>

            <div class="flex flex-col gap-2">
                <label class="flex items-start gap-3 text-sm leading-relaxed text-body">
                    <input type="checkbox" name="terms" value="1" required aria-required="true" class="mt-1 h-4 w-4 shrink-0 border-border bg-surface text-gold focus:ring-gold" @checked(old('terms'))>
                    <span>
                        I agree to the
                        <a href="{{ route('terms') }}" target="_blank" rel="noopener" class="text-gold underline decoration-gold/40 underline-offset-4 hover:text-gold-bright">Terms of Service</a>
                        and have read the
                        <a href="{{ route('privacy') }}" target="_blank" rel="noopener" class="text-gold underline decoration-gold/40 underline-offset-4 hover:text-gold-bright">Privacy Policy</a>.
                    </span>
                </label>
                @error('terms')
                    <p class="text-xs text-danger">You must accept the Terms of Service and Privacy Policy to create an account.</p>
                @enderror
            </div>

            <x-button variant="primary" type="submit">
                Create Account
                <x-icon name="arrow-right" class="h-3.5 w-3.5" />
            </x-button>
        </form>

        <p class="text-sm text-body">
            Already have an account?
            <a href="{{ route('login') }}" class="text-gold underline decoration-gold/40 underline-offset-4 hover:text-gold-bright">Log in</a>
        </p>
    </section>
</x-layout>
