<x-layout title="Log In">
    <section class="mx-auto flex max-w-md flex-col gap-10 px-4 py-16 sm:px-6 lg:py-24">
        <x-section-heading eyebrow="Member Account">
            Log In
        </x-section-heading>

        @if ($errors->has('email'))
            <p class="border border-danger/40 bg-danger/10 px-4 py-3 text-sm text-danger" role="alert">
                {{ $errors->first('email') }}
            </p>
        @endif

        <form method="POST" action="{{ route('login') }}" novalidate class="flex flex-col gap-6">
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

            <div class="flex flex-col gap-2">
                <label for="password" class="text-xs font-semibold uppercase tracking-widest text-body">
                    Password <span class="text-gold" aria-hidden="true">*</span>
                    <span class="sr-only">required</span>
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    aria-required="true"
                    class="border border-border bg-surface px-4 py-3 text-sm text-cream placeholder:text-muted focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold"
                >
            </div>

            <x-button variant="primary" type="submit">
                Log In
                <x-icon name="arrow-right" class="h-3.5 w-3.5" />
            </x-button>
        </form>

        <p class="text-sm text-body">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-gold underline decoration-gold/40 underline-offset-4 hover:text-gold-bright">Register</a>
        </p>
    </section>
</x-layout>
