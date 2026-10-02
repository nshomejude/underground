<x-layout :title="$sector->name" :description="$sector->summary">
    <section class="border-b border-border bg-surface">
        <div class="mx-auto max-w-5xl px-4 py-14 sm:px-6 lg:px-8 lg:py-20">
            <a href="{{ route('sectors.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-gold hover:text-gold-bright">
                <x-icon name="chevron-right" class="h-3.5 w-3.5 rotate-180" />
                Back to Sectors
            </a>

            <div class="mt-8 flex flex-col gap-6">
                <span class="flex h-14 w-14 items-center justify-center border border-gold text-gold">
                    <x-icon name="{{ $sector->motif }}" class="h-7 w-7" />
                </span>

                <h1 class="font-serif text-3xl font-semibold leading-tight text-cream sm:text-4xl lg:text-5xl">
                    {{ $sector->name }}
                </h1>

                <p class="max-w-3xl text-lg leading-relaxed text-body">{{ $sector->summary }}</p>
            </div>
        </div>
    </section>

    @if (! empty($content['overview']))
        <section class="border-b border-border bg-ink">
            <div class="mx-auto grid max-w-5xl grid-cols-1 gap-10 px-4 py-14 sm:px-6 lg:grid-cols-3 lg:gap-16 lg:px-8 lg:py-20">
                <x-section-heading eyebrow="Context" class="lg:col-span-1">Why We Operate Here</x-section-heading>
                <div class="flex flex-col gap-5 lg:col-span-2">
                    @foreach ($content['overview'] as $paragraph)
                        <p class="text-base leading-relaxed text-body">{{ $paragraph }}</p>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if (! empty($content['focus']))
        <section class="border-b border-border bg-surface">
            <div class="mx-auto grid max-w-5xl grid-cols-1 gap-10 px-4 py-14 sm:px-6 lg:grid-cols-3 lg:gap-16 lg:px-8 lg:py-20">
                <x-section-heading eyebrow="Focus Areas" class="lg:col-span-1">Where We Help</x-section-heading>
                <ul class="flex flex-col gap-4 lg:col-span-2">
                    @foreach ($content['focus'] as $item)
                        <li class="flex items-start gap-4 border-b border-border pb-4">
                            <x-icon name="check-circle" class="mt-0.5 h-5 w-5 shrink-0 text-gold" />
                            <span class="text-base leading-relaxed text-cream">{{ $item }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    @if (! empty($content['clients']))
        <section class="border-b border-border bg-ink">
            <div class="mx-auto max-w-5xl px-4 py-14 sm:px-6 lg:px-8 lg:py-20">
                <x-section-heading eyebrow="Who We Serve">Our Clients in This Sector</x-section-heading>
                <div class="mt-10 grid grid-cols-1 gap-px bg-border sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($content['clients'] as $client)
                        <div class="flex items-center gap-3 bg-surface p-6">
                            <x-icon name="users" class="h-5 w-5 shrink-0 text-gold" />
                            <span class="text-sm font-semibold text-cream">{{ $client }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($relatedCapabilities !== [])
        <section class="border-b border-border bg-surface">
            <div class="mx-auto max-w-5xl px-4 py-14 sm:px-6 lg:px-8 lg:py-20">
                <x-section-heading eyebrow="Capabilities">What We Bring</x-section-heading>
                <div class="mt-10 grid grid-cols-1 gap-px bg-border md:grid-cols-3">
                    @foreach ($relatedCapabilities as $capability)
                        <a href="{{ route('capabilities.show', $capability->slug->value) }}" class="group flex flex-col gap-4 bg-surface p-6 transition-colors hover:bg-surface-raised">
                            <span class="flex h-11 w-11 items-center justify-center border border-gold text-gold">
                                <x-icon name="{{ $capability->icon }}" class="h-5 w-5" />
                            </span>
                            <h3 class="font-serif text-lg font-semibold text-cream group-hover:text-gold">{{ $capability->title }}</h3>
                            <p class="text-sm leading-relaxed text-body">{{ $capability->summary }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($otherSectors !== [])
        <section class="border-b border-border bg-ink">
            <div class="mx-auto max-w-5xl px-4 py-14 sm:px-6 lg:px-8">
                <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-gold">Other Sectors</h2>
                <div class="mt-6 flex flex-wrap gap-3">
                    @foreach ($otherSectors as $other)
                        <a href="{{ route('sectors.show', $other->slug->value) }}" class="border border-border px-4 py-2 text-xs font-semibold uppercase tracking-wide text-body transition-colors hover:border-gold hover:text-gold">
                            {{ $other->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="bg-surface">
        <div class="mx-auto flex max-w-5xl flex-col items-start gap-6 px-4 py-16 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8 lg:py-20">
            <div class="flex flex-col gap-2">
                <h2 class="font-serif text-2xl font-semibold text-cream sm:text-3xl">Working in {{ $sector->name }}?</h2>
                <p class="max-w-xl text-sm leading-relaxed text-body">Tell us what you are trying to achieve. A partner will respond personally, and in confidence.</p>
            </div>
            <div class="flex flex-wrap gap-4">
                <x-button variant="primary" href="{{ route('inquiries.create') }}">
                    Start a Confidential Inquiry
                    <x-icon name="lock" class="h-3.5 w-3.5" />
                </x-button>
                <x-button variant="secondary" href="{{ route('sectors.index') }}">
                    All Sectors
                    <x-icon name="chevron-right" class="h-3.5 w-3.5" />
                </x-button>
            </div>
        </div>
    </section>
</x-layout>
