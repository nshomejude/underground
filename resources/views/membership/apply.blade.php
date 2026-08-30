<x-layout :title="'Apply — ' . $tier->name">
    <section class="mx-auto flex max-w-3xl flex-col gap-10 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center border border-gold/40 text-gold">
                <x-icon :name="$tier->icon" class="h-5 w-5" />
            </span>
            <span class="text-xs font-semibold uppercase tracking-widest text-gold">{{ $tier->name }}</span>
        </div>

        <x-section-heading eyebrow="Membership Application">
            Apply for {{ $tier->name }}
        </x-section-heading>

        <p class="max-w-2xl text-base leading-relaxed text-body">
            {{ $tier->audience }} Every application is reviewed by a partner before a tier is granted.
        </p>

        @if (session('reference'))
            <div class="flex flex-col gap-4 border border-gold/40 bg-surface px-6 py-8 sm:px-10 sm:py-10" role="status">
                <div class="flex items-center gap-3">
                    <x-icon name="check-circle" class="h-6 w-6 shrink-0 text-gold" />
                    <h3 class="font-serif text-2xl font-semibold text-cream">Application Received</h3>
                </div>

                <p class="text-sm leading-relaxed text-body">
                    Your application for {{ $tier->name }} has been logged and is under review. Keep the
                    reference below for your records.
                </p>

                <p class="inline-flex w-fit items-center gap-2 border border-border bg-ink px-4 py-2 font-mono text-sm tracking-wider text-gold-bright">
                    {{ session('reference') }}
                </p>

                <p class="text-xs leading-relaxed text-muted">
                    You can check on this application anytime at
                    <a href="{{ route('membership.track', ['reference' => session('reference')]) }}" class="text-gold underline hover:text-gold-bright">
                        {{ route('membership.track') }}
                    </a>
                    &mdash; no account required.
                </p>

                <x-button variant="secondary" href="{{ route('membership.index') }}" class="w-fit">
                    Back to Membership
                </x-button>
            </div>
        @else
            <form method="POST" action="{{ route('membership.store', ['tier' => $tier->slug->value]) }}" novalidate class="flex flex-col gap-6">
                @csrf

                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="flex flex-col gap-2">
                        <label for="applicant_name" class="text-xs font-semibold uppercase tracking-widest text-body">
                            Full Name <span class="text-gold" aria-hidden="true">*</span>
                            <span class="sr-only">required</span>
                        </label>
                        <input
                            type="text"
                            id="applicant_name"
                            name="applicant_name"
                            value="{{ old('applicant_name') }}"
                            required
                            aria-required="true"
                            @if ($errors->has('applicant_name')) aria-invalid="true" aria-describedby="applicant_name-error" @endif
                            class="border border-border bg-surface px-4 py-3 text-sm text-cream placeholder:text-muted focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold"
                        >
                        @error('applicant_name')
                            <p id="applicant_name-error" class="text-xs text-danger">{{ $message }}</p>
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

                    <div class="flex flex-col gap-2 sm:col-span-2">
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
                </div>

                <div class="flex flex-col gap-2">
                    <div class="flex items-baseline justify-between">
                        <label for="statement" class="text-xs font-semibold uppercase tracking-widest text-body">
                            Statement <span class="text-gold" aria-hidden="true">*</span>
                            <span class="sr-only">required, minimum 40 characters</span>
                        </label>
                        <span
                            data-char-counter-output="statement"
                            class="text-[11px] uppercase tracking-widest text-muted"
                            aria-live="polite"
                        >0 / 40 min</span>
                    </div>
                    <textarea
                        id="statement"
                        name="statement"
                        rows="6"
                        required
                        aria-required="true"
                        minlength="40"
                        data-char-counter
                        data-char-counter-min="40"
                        @if ($errors->has('statement')) aria-invalid="true" aria-describedby="statement-error" @endif
                        class="border border-border bg-surface px-4 py-3 text-sm text-cream placeholder:text-muted focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold"
                        placeholder="Tell us who you represent and why this tier is the right fit."
                    >{{ old('statement') }}</textarea>
                    @error('statement')
                        <p id="statement-error" class="text-xs text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-button variant="primary" type="submit">
                        Submit Application
                        <x-icon name="lock" class="h-3.5 w-3.5" />
                    </x-button>
                </div>
            </form>
        @endif
    </section>
</x-layout>
