<x-layout title="Confidential Inquiry">
    <section class="mx-auto flex max-w-3xl flex-col gap-10 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading eyebrow="Discreet. Strategic. Effective.">
            Start a Confidential Conversation
        </x-section-heading>

        <p class="max-w-2xl text-base leading-relaxed text-body">
            Underground operates where decisions are made and outcomes are shaped. Share the outline of
            your situation below and a partner will respond directly &mdash; nothing you tell us here
            leaves this conversation without your consent.
        </p>

        @if (session('reference'))
            <div class="flex flex-col gap-4 border border-gold/40 bg-surface px-6 py-8 sm:px-10 sm:py-10" role="status">
                <div class="flex items-center gap-3">
                    <x-icon name="check-circle" class="h-6 w-6 shrink-0 text-gold" />
                    <h3 class="font-serif text-2xl font-semibold text-cream">Inquiry Received</h3>
                </div>

                <p class="text-sm leading-relaxed text-body">
                    Your confidential inquiry has been logged. A partner will be in touch shortly. Keep
                    the reference below for your records.
                </p>

                <p class="inline-flex w-fit items-center gap-2 border border-border bg-ink px-4 py-2 font-mono text-sm tracking-wider text-gold-bright">
                    {{ session('reference') }}
                </p>

                <p class="text-xs leading-relaxed text-muted">
                    You can check on this inquiry anytime at
                    <a href="{{ route('inquiries.track', ['reference' => session('reference')]) }}" class="text-gold underline hover:text-gold-bright">
                        {{ route('inquiries.track') }}
                    </a>
                    &mdash; no account required.
                </p>
            </div>
        @else
            <form method="POST" action="{{ route('inquiries.store') }}" novalidate class="flex flex-col gap-6">
                @csrf

                <div class="grid gap-6 sm:grid-cols-2">
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
                            required
                            aria-required="true"
                            @if ($errors->has('name')) aria-invalid="true" aria-describedby="name-error" @endif
                            class="border border-border bg-surface px-4 py-3 text-sm text-cream placeholder:text-muted focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold"
                        >
                        @error('name')
                            <p id="name-error" class="text-xs text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="organisation" class="text-xs font-semibold uppercase tracking-widest text-body">
                            Organisation
                        </label>
                        <input
                            type="text"
                            id="organisation"
                            name="organisation"
                            value="{{ old('organisation') }}"
                            @if ($errors->has('organisation')) aria-invalid="true" aria-describedby="organisation-error" @endif
                            class="border border-border bg-surface px-4 py-3 text-sm text-cream placeholder:text-muted focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold"
                        >
                        @error('organisation')
                            <p id="organisation-error" class="text-xs text-danger">{{ $message }}</p>
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
                        <label for="phone" class="text-xs font-semibold uppercase tracking-widest text-body">
                            Phone
                        </label>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            value="{{ old('phone') }}"
                            @if ($errors->has('phone')) aria-invalid="true" aria-describedby="phone-error" @endif
                            class="border border-border bg-surface px-4 py-3 text-sm text-cream placeholder:text-muted focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold"
                        >
                        @error('phone')
                            <p id="phone-error" class="text-xs text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="country" class="text-xs font-semibold uppercase tracking-widest text-body">
                            Country
                        </label>
                        <input
                            type="text"
                            id="country"
                            name="country"
                            value="{{ old('country') }}"
                            @if ($errors->has('country')) aria-invalid="true" aria-describedby="country-error" @endif
                            class="border border-border bg-surface px-4 py-3 text-sm text-cream placeholder:text-muted focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold"
                        >
                        @error('country')
                            <p id="country-error" class="text-xs text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="interest" class="text-xs font-semibold uppercase tracking-widest text-body">
                            Area of Interest <span class="text-gold" aria-hidden="true">*</span>
                            <span class="sr-only">required</span>
                        </label>
                        <select
                            id="interest"
                            name="interest"
                            required
                            aria-required="true"
                            @if ($errors->has('interest')) aria-invalid="true" aria-describedby="interest-error" @endif
                            class="border border-border bg-surface px-4 py-3 text-sm text-cream focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold"
                        >
                            <option value="" disabled {{ old('interest') ? '' : 'selected' }}>Select an area&hellip;</option>
                            @foreach ($interestAreas as $area)
                                <option value="{{ $area['value'] }}" @selected(old('interest') === $area['value'])>
                                    {{ $area['label'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('interest')
                            <p id="interest-error" class="text-xs text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <div class="flex items-baseline justify-between">
                        <label for="brief" class="text-xs font-semibold uppercase tracking-widest text-body">
                            Brief <span class="text-gold" aria-hidden="true">*</span>
                            <span class="sr-only">required, minimum 20 characters</span>
                        </label>
                        <span
                            data-char-counter-output="brief"
                            class="text-[11px] uppercase tracking-widest text-muted"
                            aria-live="polite"
                        >0 / 20 min</span>
                    </div>
                    <textarea
                        id="brief"
                        name="brief"
                        rows="6"
                        required
                        aria-required="true"
                        minlength="20"
                        data-char-counter
                        data-char-counter-min="20"
                        @if ($errors->has('brief')) aria-invalid="true" aria-describedby="brief-error" @endif
                        class="border border-border bg-surface px-4 py-3 text-sm text-cream placeholder:text-muted focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold"
                        placeholder="Describe the situation, the outcome you need, and the timeline you're working against."
                    >{{ old('brief') }}</textarea>
                    @error('brief')
                        <p id="brief-error" class="text-xs text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-button variant="primary" type="submit">
                        Submit Inquiry
                        <x-icon name="lock" class="h-3.5 w-3.5" />
                    </x-button>
                </div>
            </form>
        @endif
    </section>
</x-layout>
