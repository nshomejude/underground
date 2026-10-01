<x-layout :title="$capability->title">
    {{-- Header --}}
    <section class="border-b border-border bg-surface">
        <div class="mx-auto max-w-5xl px-4 py-14 sm:px-6 lg:px-8 lg:py-20">
            <a href="{{ route('capabilities.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-gold hover:text-gold-bright">
                <x-icon name="chevron-right" class="h-3.5 w-3.5 rotate-180" />
                Back to Capabilities
            </a>

            <div class="mt-8 flex flex-col gap-6">
                <span class="flex h-14 w-14 items-center justify-center border border-gold text-gold">
                    <x-icon name="{{ $capability->icon }}" class="h-7 w-7" />
                </span>

                <div class="flex flex-col gap-3">
                    @if ($capability->isFeatured)
                        <x-status-badge label="Featured Capability" tone="info" />
                    @endif

                    <h1 class="font-serif text-3xl font-semibold leading-tight text-cream sm:text-4xl lg:text-5xl">
                        {{ $capability->title }}
                    </h1>
                </div>

                <p class="max-w-3xl text-lg leading-relaxed text-body">{{ $capability->summary }}</p>
            </div>
        </div>
    </section>

    {{-- Overview --}}
    @if (! empty($content['overview']))
        <section class="border-b border-border bg-ink">
            <div class="mx-auto grid max-w-5xl grid-cols-1 gap-10 px-4 py-14 sm:px-6 lg:grid-cols-3 lg:gap-16 lg:px-8 lg:py-20">
                <x-section-heading eyebrow="Overview" class="lg:col-span-1">The Practice</x-section-heading>
                <div class="flex flex-col gap-5 lg:col-span-2">
                    @foreach ($content['overview'] as $paragraph)
                        <p class="text-base leading-relaxed text-body">{{ $paragraph }}</p>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- What we do --}}
    @if (! empty($content['services']))
        <section class="border-b border-border bg-surface">
            <div class="mx-auto max-w-5xl px-4 py-14 sm:px-6 lg:px-8 lg:py-20">
                <x-section-heading eyebrow="What We Do">How We Help</x-section-heading>

                <div class="mt-10 grid grid-cols-1 gap-px bg-border sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($content['services'] as $title => $text)
                        <div class="flex flex-col gap-3 bg-surface p-6">
                            <span class="font-serif text-sm font-semibold text-gold">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <h3 class="font-serif text-lg font-semibold leading-snug text-cream">{{ $title }}</h3>
                            <p class="text-sm leading-relaxed text-body">{{ $text }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Approach --}}
    @if (! empty($content['approach']))
        <section class="border-b border-border bg-ink">
            <div class="mx-auto max-w-5xl px-4 py-14 sm:px-6 lg:px-8 lg:py-20">
                <x-section-heading eyebrow="Our Approach">From First Conversation to Lasting Result</x-section-heading>

                <ol class="mt-10 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($content['approach'] as $step => $text)
                        <li class="flex flex-col gap-3 border-t-2 border-gold pt-5">
                            <span class="text-xs font-semibold uppercase tracking-[0.25em] text-muted">Step {{ $loop->iteration }}</span>
                            <h3 class="font-serif text-xl font-semibold text-cream">{{ $step }}</h3>
                            <p class="text-sm leading-relaxed text-body">{{ $text }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>
    @endif

    {{-- Outcomes --}}
    @if (! empty($content['outcomes']))
        <section class="border-b border-border bg-surface">
            <div class="mx-auto grid max-w-5xl grid-cols-1 gap-10 px-4 py-14 sm:px-6 lg:grid-cols-3 lg:gap-16 lg:px-8 lg:py-20">
                <x-section-heading eyebrow="Outcomes" class="lg:col-span-1">What Clients Gain</x-section-heading>
                <ul class="flex flex-col gap-4 lg:col-span-2">
                    @foreach ($content['outcomes'] as $outcome)
                        <li class="flex items-start gap-4 border-b border-border pb-4">
                            <x-icon name="check-circle" class="mt-0.5 h-5 w-5 shrink-0 text-gold" />
                            <span class="text-base leading-relaxed text-cream">{{ $outcome }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    {{-- Related --}}
    @if ($relatedSectors !== [] || $relatedEngagementModels !== [])
        <section class="border-b border-border bg-ink">
            <div class="mx-auto grid max-w-5xl grid-cols-1 gap-12 px-4 py-14 sm:px-6 lg:grid-cols-2 lg:px-8 lg:py-20">
                @if ($relatedSectors !== [])
                    <div>
                        <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-gold">Sectors Where We Apply This</h2>
                        <ul class="mt-5 flex flex-col divide-y divide-border border-y border-border">
                            @foreach ($relatedSectors as $sector)
                                <li>
                                    <a href="{{ route('sectors.show', $sector->slug->value) }}" class="group flex items-center gap-4 py-4">
                                        <x-icon name="{{ $sector->motif }}" class="h-5 w-5 shrink-0 text-gold" />
                                        <span class="flex-1 text-sm font-semibold text-cream group-hover:text-gold">{{ $sector->name }}</span>
                                        <x-icon name="chevron-right" class="h-4 w-4 text-gold" />
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($relatedEngagementModels !== [])
                    <div>
                        <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-gold">How You Can Engage Us</h2>
                        <ul class="mt-5 flex flex-col divide-y divide-border border-y border-border">
                            @foreach ($relatedEngagementModels as $model)
                                <li>
                                    <a href="{{ route('engagement-models.show', $model->slug->value) }}" class="group flex items-center gap-4 py-4">
                                        <x-icon name="{{ $model->icon }}" class="h-5 w-5 shrink-0 text-gold" />
                                        <span class="flex-1 text-sm font-semibold text-cream group-hover:text-gold">{{ $model->name }}</span>
                                        <x-icon name="chevron-right" class="h-4 w-4 text-gold" />
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- Other capabilities --}}
    @if ($otherCapabilities !== [])
        <section class="border-b border-border bg-surface">
            <div class="mx-auto max-w-5xl px-4 py-14 sm:px-6 lg:px-8">
                <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-gold">More Capabilities</h2>
                <div class="mt-6 flex flex-wrap gap-3">
                    @foreach ($otherCapabilities as $other)
                        <a href="{{ route('capabilities.show', $other->slug->value) }}" class="border border-border px-4 py-2 text-xs font-semibold uppercase tracking-wide text-body transition-colors hover:border-gold hover:text-gold">
                            {{ $other->title }}
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- CTA --}}
    <section class="bg-ink">
        <div class="mx-auto flex max-w-5xl flex-col items-start gap-6 px-4 py-16 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8 lg:py-20">
            <div class="flex flex-col gap-2">
                <h2 class="font-serif text-2xl font-semibold text-cream sm:text-3xl">Discuss {{ $capability->title }} in confidence.</h2>
                <p class="max-w-xl text-sm leading-relaxed text-body">A partner reviews every inquiry personally. Nothing you share leaves the firm.</p>
            </div>
            <div class="flex flex-wrap gap-4">
                <x-button variant="primary" href="{{ route('inquiries.create') }}">
                    Start a Confidential Inquiry
                    <x-icon name="lock" class="h-3.5 w-3.5" />
                </x-button>
                <x-button variant="secondary" href="{{ route('capabilities.index') }}">
                    All Capabilities
                    <x-icon name="chevron-right" class="h-3.5 w-3.5" />
                </x-button>
            </div>
        </div>
    </section>
</x-layout>
