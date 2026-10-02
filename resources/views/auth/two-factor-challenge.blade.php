<x-layout title="Two-Factor Verification">
    <section class="mx-auto flex max-w-md flex-col gap-10 px-4 py-16 sm:px-6 lg:py-24">
        <a href="{{ url('/') }}" class="mx-auto inline-flex">
            <x-brand-mark />
        </a>

        <x-section-heading tag="h1" eyebrow="Member Account">
            Verify It's You
        </x-section-heading>

        <p class="text-sm text-body">Enter the 6-digit code from your authenticator app to finish signing in.</p>

        <div aria-live="assertive">
            @if ($errors->any())
                <p class="border border-danger/40 bg-danger/10 px-4 py-3 text-sm text-danger" role="alert">
                    {{ $errors->first() }}
                </p>
            @endif
        </div>

        <form method="POST" action="{{ route('two-factor.login') }}" novalidate class="flex flex-col gap-6">
            @csrf

            <div id="code-block" class="flex flex-col gap-2" @if (old('recovery_code') || $errors->has('recovery_code')) hidden @endif>
                <label for="code" class="text-xs font-semibold uppercase tracking-widest text-body">
                    Authentication code <span class="text-gold" aria-hidden="true">*</span>
                    <span class="sr-only">required</span>
                </label>
                <input
                    type="text"
                    id="code"
                    name="code"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    pattern="[0-9 ]*"
                    maxlength="7"
                    autofocus
                    class="min-h-11 border border-border bg-surface px-4 py-3 text-center text-lg tracking-[0.3em] text-cream placeholder:text-muted focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold"
                >
            </div>

            <div id="recovery-block" class="flex flex-col gap-2" @unless (old('recovery_code') || $errors->has('recovery_code')) hidden @endunless>
                <label for="recovery_code" class="text-xs font-semibold uppercase tracking-widest text-body">
                    Recovery code
                </label>
                <input
                    type="text"
                    id="recovery_code"
                    name="recovery_code"
                    autocomplete="off"
                    autocapitalize="none"
                    spellcheck="false"
                    placeholder="xxxxx-xxxxx"
                    maxlength="30"
                    class="min-h-11 border border-border bg-surface px-4 py-3 text-sm text-cream placeholder:text-muted focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold"
                >
            </div>

            <x-button variant="primary" type="submit">
                Verify
                <x-icon name="arrow-right" class="h-3.5 w-3.5" />
            </x-button>

            <button type="button" id="toggle-recovery" class="inline-flex min-h-11 items-center self-start text-sm text-gold underline decoration-gold/40 underline-offset-4 hover:text-gold-bright" aria-controls="recovery-block code-block">
                Use a recovery code instead
            </button>
        </form>

        <noscript><style>#recovery-block { display: flex !important; } #toggle-recovery { display: none; }</style></noscript>

        <p class="text-sm text-body">
            <a href="{{ route('login') }}" class="inline-flex min-h-11 items-center text-gold underline decoration-gold/40 underline-offset-4 hover:text-gold-bright">Back to log in</a>
        </p>
    </section>

    <script>
        (function () {
            var btn = document.getElementById('toggle-recovery');
            var codeBlock = document.getElementById('code-block');
            var recBlock = document.getElementById('recovery-block');
            var code = document.getElementById('code');
            var rec = document.getElementById('recovery_code');
            function show(useRecovery) {
                recBlock.hidden = !useRecovery;
                codeBlock.hidden = useRecovery;
                btn.textContent = useRecovery ? 'Use an authenticator code instead' : 'Use a recovery code instead';
                if (useRecovery) { code.value = ''; rec.focus(); } else { rec.value = ''; code.focus(); }
            }
            btn.addEventListener('click', function () { show(recBlock.hidden); });
            if (!recBlock.hidden) btn.textContent = 'Use an authenticator code instead';
        })();
    </script>
</x-layout>
