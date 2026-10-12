<x-layout title="Consultation Desk">
    <section class="mx-auto flex max-w-3xl flex-col gap-10 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading tag="h1" eyebrow="Free Qualification">
            Underground Consultation Desk
        </x-section-heading>

        <p class="max-w-2xl text-base leading-relaxed text-body">
            The Consultation Desk replaces the unstructured exploratory call. Present your project
            through the form below &mdash; there is no cost to submit. After an initial review, a
            partner will direct you to the consultation format that fits, with a clear scope and a
            summary of next steps. Submitting this form does not commit you to anything, and does not
            guarantee acceptance.
        </p>

        @if (session('reference'))
            <div class="flex flex-col gap-4 border border-gold/40 bg-surface px-6 py-8 sm:px-10 sm:py-10" role="status">
                <div class="flex items-center gap-3">
                    <x-icon name="check-circle" class="h-6 w-6 shrink-0 text-gold" />
                    <h3 class="font-serif text-2xl font-semibold text-cream">Request Received</h3>
                </div>

                <p class="text-sm leading-relaxed text-body">
                    Your qualification request has been logged for review. A partner will follow up by
                    email with next steps &mdash; including whether a paid consultation is the right
                    fit and, if so, how to arrange it. Keep the reference below for your records.
                </p>

                <p class="inline-flex w-fit items-center gap-2 border border-border bg-ink px-4 py-2 font-mono text-sm tracking-wider text-gold-bright">
                    {{ session('reference') }}
                </p>

                <p class="text-xs leading-relaxed text-muted">
                    You can check on this request anytime at
                    <a href="{{ route('consultations.track', ['reference' => session('reference')]) }}" class="text-gold underline hover:text-gold-bright">
                        {{ route('consultations.track') }}
                    </a>
                    &mdash; no account required.
                </p>
            </div>
        @else
            <form method="POST" action="{{ route('consultations.store') }}" novalidate class="flex flex-col gap-6">
                @csrf

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
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
                        <label for="role" class="text-xs font-semibold uppercase tracking-widest text-body">
                            Role / Title
                        </label>
                        <input
                            type="text"
                            id="role"
                            name="role"
                            value="{{ old('role') }}"
                            @if ($errors->has('role')) aria-invalid="true" aria-describedby="role-error" @endif
                            class="border border-border bg-surface px-4 py-3 text-sm text-cream placeholder:text-muted focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold"
                        >
                        @error('role')
                            <p id="role-error" class="text-xs text-danger">{{ $message }}</p>
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
                        <label for="sector" class="text-xs font-semibold uppercase tracking-widest text-body">
                            Sector <span class="text-gold" aria-hidden="true">*</span>
                            <span class="sr-only">required</span>
                        </label>
                        <select
                            id="sector"
                            name="sector"
                            required
                            aria-required="true"
                            @if ($errors->has('sector')) aria-invalid="true" aria-describedby="sector-error" @endif
                            class="border border-border bg-surface px-4 py-3 text-sm text-cream focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold"
                        >
                            <option value="" disabled {{ old('sector') ? '' : 'selected' }}>Select a sector&hellip;</option>
                            @foreach ($sectors as $sector)
                                <option value="{{ $sector['value'] }}" @selected(old('sector') === $sector['value'])>
                                    {{ $sector['label'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('sector')
                            <p id="sector-error" class="text-xs text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="maturity_stage" class="text-xs font-semibold uppercase tracking-widest text-body">
                            Maturity Stage <span class="text-gold" aria-hidden="true">*</span>
                            <span class="sr-only">required</span>
                        </label>
                        <select
                            id="maturity_stage"
                            name="maturity_stage"
                            required
                            aria-required="true"
                            @if ($errors->has('maturity_stage')) aria-invalid="true" aria-describedby="maturity_stage-error" @endif
                            class="border border-border bg-surface px-4 py-3 text-sm text-cream focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold"
                        >
                            <option value="" disabled {{ old('maturity_stage') ? '' : 'selected' }}>Select a stage&hellip;</option>
                            @foreach ($maturityStages as $stage)
                                <option value="{{ $stage['value'] }}" @selected(old('maturity_stage') === $stage['value'])>
                                    {{ $stage['label'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('maturity_stage')
                            <p id="maturity_stage-error" class="text-xs text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="budget_range" class="text-xs font-semibold uppercase tracking-widest text-body">
                            Indicative Budget Range <span class="text-gold" aria-hidden="true">*</span>
                            <span class="sr-only">required</span>
                        </label>
                        <select
                            id="budget_range"
                            name="budget_range"
                            required
                            aria-required="true"
                            @if ($errors->has('budget_range')) aria-invalid="true" aria-describedby="budget_range-error" @endif
                            class="border border-border bg-surface px-4 py-3 text-sm text-cream focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold"
                        >
                            <option value="" disabled {{ old('budget_range') ? '' : 'selected' }}>Select a range&hellip;</option>
                            @foreach ($budgetRanges as $range)
                                <option value="{{ $range['value'] }}" @selected(old('budget_range') === $range['value'])>
                                    {{ $range['label'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('budget_range')
                            <p id="budget_range-error" class="text-xs text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <div class="flex items-baseline justify-between">
                        <label for="project_description" class="text-xs font-semibold uppercase tracking-widest text-body">
                            Project Description <span class="text-gold" aria-hidden="true">*</span>
                            <span class="sr-only">required, minimum 20 characters</span>
                        </label>
                        <span
                            data-char-counter-output="project_description"
                            class="text-[11px] uppercase tracking-widest text-muted"
                            aria-live="polite"
                        >0 / 20 min</span>
                    </div>
                    <textarea
                        id="project_description"
                        name="project_description"
                        rows="5"
                        required
                        aria-required="true"
                        minlength="20"
                        data-char-counter
                        data-char-counter-min="20"
                        @if ($errors->has('project_description')) aria-invalid="true" aria-describedby="project_description-error" @endif
                        class="border border-border bg-surface px-4 py-3 text-sm text-cream placeholder:text-muted focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold"
                        placeholder="Describe the project or situation this consultation would concern."
                    >{{ old('project_description') }}</textarea>
                    @error('project_description')
                        <p id="project_description-error" class="text-xs text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col gap-2">
                    <div class="flex items-baseline justify-between">
                        <label for="need_description" class="text-xs font-semibold uppercase tracking-widest text-body">
                            Need Description <span class="text-gold" aria-hidden="true">*</span>
                            <span class="sr-only">required, minimum 20 characters</span>
                        </label>
                        <span
                            data-char-counter-output="need_description"
                            class="text-[11px] uppercase tracking-widest text-muted"
                            aria-live="polite"
                        >0 / 20 min</span>
                    </div>
                    <textarea
                        id="need_description"
                        name="need_description"
                        rows="5"
                        required
                        aria-required="true"
                        minlength="20"
                        data-char-counter
                        data-char-counter-min="20"
                        @if ($errors->has('need_description')) aria-invalid="true" aria-describedby="need_description-error" @endif
                        class="border border-border bg-surface px-4 py-3 text-sm text-cream placeholder:text-muted focus:border-gold focus:outline-none focus:ring-1 focus:ring-gold"
                        placeholder="Describe what you need from a consultation and the timeline you're working against."
                    >{{ old('need_description') }}</textarea>
                    @error('need_description')
                        <p id="need_description-error" class="text-xs text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-start gap-3 border border-border bg-surface px-4 py-3">
                    <input
                        type="checkbox"
                        id="has_supporting_documents"
                        name="has_supporting_documents"
                        value="1"
                        @checked(old('has_supporting_documents'))
                        class="mt-0.5 h-4 w-4 shrink-0 border-border bg-ink text-gold focus:ring-1 focus:ring-gold"
                    >
                    <label for="has_supporting_documents" class="text-sm leading-relaxed text-body">
                        I have supporting documents available (business plan, financials, mandate letter,
                        etc.) that I could share later in the process.
                        <span class="block text-xs text-muted">No upload is needed at this stage &mdash; this is a qualification step only.</span>
                    </label>
                </div>

                <div>
                    <x-button variant="primary" type="submit">
                        Submit for Review
                        <x-icon name="send" class="h-3.5 w-3.5" />
                    </x-button>
                </div>
            </form>
        @endif
    </section>

    <x-value-grid
        tone="surface"
        eyebrow="Before You Submit"
        heading="What to Expect"
        :items="[
            ['icon' => 'scan-line', 'title' => 'An initial review', 'text' => 'A member of staff reviews every request and directs you to the consultation format that fits.'],
            ['icon' => 'lock', 'title' => 'No sensitive documents yet', 'text' => 'This step is a qualification form only. Confidential or sensitive materials are never requested here.'],
            ['icon' => 'users', 'title' => 'Track your reference', 'text' => 'You will receive an opaque reference number and can follow your request on the tracker with no account needed.'],
        ]"
    />

    <x-faq
        tone="ink"
        eyebrow="Your Request"
        heading="Common Questions"
        :items="[
            ['q' => 'Is there a cost to submit this form?', 'a' => 'No. The Consultation Desk is a free qualification step. Any paid consultation that follows is a separate, later step you would be invited into, never a surprise charge.'],
            ['q' => 'What happens after I submit?', 'a' => 'A partner reviews your request and follows up by email with next steps, including whether a paid consultation is the right fit and, if so, how it would work. Submitting a request does not guarantee acceptance.'],
            ['q' => 'When will I hear back?', 'a' => 'Use the tracker to follow progress. We cannot promise a fixed time, but every request is reviewed personally.'],
            ['q' => 'I would prefer to email.', 'a' => 'For general questions you can write to info@un-der.com.'],
        ]"
    />
</x-layout>
