<x-layout title="Log In">
    <section class="mx-auto flex max-w-md flex-col gap-10 px-4 py-16 sm:px-6 lg:py-24">
        <a href="{{ url('/') }}" class="mx-auto inline-flex">
            <x-brand-mark />
        </a>

        <x-section-heading eyebrow="Member Account">
            Log In
        </x-section-heading>

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

        @if (session('error'))
            <p class="border border-danger/40 bg-danger/10 px-4 py-3 text-sm text-danger" role="alert">
                {{ session('error') }}
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
                    autocomplete="username"
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
                    autocomplete="current-password"
                    required
                    aria-required="true"
                    class="border border-border bg-surface px-4 py-3 text-sm text-cream placeholder:text-muted focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold"
                >
            </div>

            <div class="flex items-center justify-between gap-4">
                <label class="inline-flex items-center gap-2 text-sm text-body">
                    <input type="checkbox" name="remember" value="1" class="h-4 w-4 border-border bg-surface text-gold focus:ring-gold" @checked(old('remember'))>
                    Keep me signed in
                </label>
                <a href="{{ route('password.request') }}" class="text-sm text-gold underline decoration-gold/40 underline-offset-4 hover:text-gold-bright">Forgot password?</a>
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
