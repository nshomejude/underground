<x-layout title="Forgot Password">
    <section class="mx-auto flex max-w-md flex-col gap-10 px-4 py-16 sm:px-6 lg:py-24">
        <x-section-heading eyebrow="Member Account">
            Forgot Password
        </x-section-heading>

        <p class="text-sm leading-relaxed text-body">
            Enter the email address on your account and, if it matches a member account, we will
            send a link to reset your password.
        </p>

        @if (session('status'))
            <p class="flex items-center gap-2 border border-success/40 bg-success/10 px-4 py-3 text-sm text-success" role="status">
                <x-icon name="check-circle" class="h-4 w-4 shrink-0" />
                {{ session('status') }}
            </p>
        @endif

        @if ($errors->has('email'))
            <p class="border border-danger/40 bg-danger/10 px-4 py-3 text-sm text-danger" role="alert">
                {{ $errors->first('email') }}
            </p>
        @endif

        <form method="POST" action="{{ route('password.email') }}" novalidate class="flex flex-col gap-6">
            @csrf

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
                    required
                    aria-required="true"
                    autofocus
                    class="border border-border bg-surface px-4 py-3 text-sm text-cream placeholder:text-muted focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold"
                >
            </div>

            <x-button variant="primary" type="submit">
                Send Reset Link
                <x-icon name="mail" class="h-3.5 w-3.5" />
            </x-button>
        </form>

        <p class="text-sm text-body">
            Remembered your password?
            <a href="{{ route('login') }}" class="text-gold underline decoration-gold/40 underline-offset-4 hover:text-gold-bright">Log in</a>
        </p>
    </section>
</x-layout>
