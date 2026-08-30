<x-layout title="Reset Password">
    <section class="mx-auto flex max-w-md flex-col gap-10 px-4 py-16 sm:px-6 lg:py-24">
        <x-section-heading eyebrow="Member Account">
            Reset Password
        </x-section-heading>

        <p class="text-sm leading-relaxed text-body">
            Choose a new password for your account.
        </p>

        @if ($errors->has('email'))
            <p class="border border-danger/40 bg-danger/10 px-4 py-3 text-sm text-danger" role="alert">
                {{ $errors->first('email') }}
            </p>
        @endif

        <form method="POST" action="{{ route('password.update') }}" novalidate class="flex flex-col gap-6">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="flex flex-col gap-2">
                <label for="email" class="text-xs font-semibold uppercase tracking-widest text-body">
                    Email <span class="text-gold" aria-hidden="true">*</span>
                    <span class="sr-only">required</span>
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email', $email) }}"
                    required
                    aria-required="true"
                    autofocus
                    class="border border-border bg-surface px-4 py-3 text-sm text-cream placeholder:text-muted focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold"
                >
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
                    @if ($errors->has('password')) aria-invalid="true" aria-describedby="password-error" @endif
                    class="border border-border bg-surface px-4 py-3 text-sm text-cream placeholder:text-muted focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold"
                >
                @error('password')
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
                    class="border border-border bg-surface px-4 py-3 text-sm text-cream placeholder:text-muted focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold"
                >
            </div>

            <x-button variant="primary" type="submit">
                Reset Password
                <x-icon name="lock" class="h-3.5 w-3.5" />
            </x-button>
        </form>
    </section>
</x-layout>
