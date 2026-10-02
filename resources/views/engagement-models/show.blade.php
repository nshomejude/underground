@php
    $seoSchema = [[
        '@type' => 'Service',
        'name' => $engagementModel->name,
        'description' => $engagementModel->summary,
        'url' => route('engagement-models.show', $engagementModel->slug->value),
        'provider' => ['@id' => url('/').'#organization'],
        'areaServed' => config('seo.organization.area_served'),
    ]];
@endphp
<x-layout :title="$engagementModel->name" :description="$engagementModel->summary" :schema="$seoSchema">
    <section class="border-b border-border bg-surface">
        <div class="mx-auto max-w-5xl px-4 py-14 sm:px-6 lg:px-8 lg:py-20">
            <a href="{{ route('engagement-models.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-gold hover:text-gold-bright">
                <x-icon name="chevron-right" class="h-3.5 w-3.5 rotate-180" />
                Back to Engagement Models
            </a>

            <div class="mt-8 flex flex-col gap-6">
                <span class="flex h-14 w-14 items-center justify-center border border-gold text-gold">
                    <x-icon name="{{ $engagementModel->icon }}" class="h-7 w-7" />
                </span>

                <h1 class="font-serif text-3xl font-semibold leading-tight text-cream sm:text-4xl lg:text-5xl">
                    {{ $engagementModel->name }}
                </h1>

                <p class="max-w-3xl text-lg leading-relaxed text-body">{{ $engagementModel->summary }}</p>
            </div>
        </div>
    </section>

    @if (! empty($content['overview']))
        <section class="border-b border-border bg-ink">
            <div class="mx-auto grid max-w-5xl grid-cols-1 gap-10 px-4 py-14 sm:px-6 lg:grid-cols-3 lg:gap-16 lg:px-8 lg:py-20">
                <x-section-heading eyebrow="Overview" class="lg:col-span-1">How It Works</x-section-heading>
                <div class="flex flex-col gap-5 lg:col-span-2">
                    @foreach ($content['overview'] as $paragraph)
                        <p class="text-base leading-relaxed text-body">{{ $paragraph }}</p>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if (! empty($content['suited_for']) || ! empty($content['includes']))
        <section class="border-b border-border bg-surface">
            <div class="mx-auto grid max-w-5xl grid-cols-1 gap-12 px-4 py-14 sm:px-6 lg:grid-cols-2 lg:gap-16 lg:px-8 lg:py-20">
                @if (! empty($content['suited_for']))
                    <div>
                        <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-gold">Best Suited For</h2>
                        <ul class="mt-6 flex flex-col gap-4">
                            @foreach ($content['suited_for'] as $item)
                                <li class="flex items-start gap-4 border-b border-border pb-4">
                                    <x-icon name="target" class="mt-0.5 h-5 w-5 shrink-0 text-gold" />
                                    <span class="text-base leading-relaxed text-cream">{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (! empty($content['includes']))
                    <div>
                        <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-gold">What Is Included</h2>
                        <ul class="mt-6 flex flex-col gap-4">
                            @foreach ($content['includes'] as $item)
                                <li class="flex items-start gap-4 border-b border-border pb-4">
                                    <x-icon name="check-circle" class="mt-0.5 h-5 w-5 shrink-0 text-gold" />
                                    <span class="text-base leading-relaxed text-cream">{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </section>
    @endif

    @if (! empty($content['process']))
        <section class="border-b border-border bg-ink">
            <div class="mx-auto max-w-5xl px-4 py-14 sm:px-6 lg:px-8 lg:py-20">
                <x-section-heading eyebrow="The Process">From Brief to Delivery</x-section-heading>

                <ol class="mt-10 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($content['process'] as $step => $text)
                        <li class="flex flex-col gap-3 border-t-2 border-gold pt-5">
                            <span class="text-xs font-semibold uppercase tracking-[0.25em] text-muted">Step {{ $loop->iteration }}</span>
                            <h3 class="font-serif text-xl font-semibold text-cream">{{ $step }}</h3>
                            <p class="text-sm leading-relaxed text-body">{{ $text }}</p>
                        </li>
                    @endforeach
                </ol>

                @if (! empty($content['commitment']))
                    <div class="mt-12 flex items-start gap-4 border border-gold/40 bg-surface p-6">
                        <x-icon name="clock" class="mt-0.5 h-5 w-5 shrink-0 text-gold" />
                        <div class="flex flex-col gap-1">
                            <p class="text-xs font-semibold uppercase tracking-widest text-gold">Commitment &amp; Terms</p>
                            <p class="text-sm leading-relaxed text-body">{{ $content['commitment'] }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </section>
    @endif

    @if ($otherModels !== [])
        <section class="border-b border-border bg-surface">
            <div class="mx-auto max-w-5xl px-4 py-14 sm:px-6 lg:px-8">
                <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-gold">Other Ways to Engage</h2>
                <div class="mt-6 flex flex-wrap gap-3">
                    @foreach ($otherModels as $other)
                        <a href="{{ route('engagement-models.show', $other->slug->value) }}" class="border border-border px-4 py-2 text-xs font-semibold uppercase tracking-wide text-body transition-colors hover:border-gold hover:text-gold">
                            {{ $other->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="bg-ink">
        <div class="mx-auto flex max-w-5xl flex-col items-start gap-6 px-4 py-16 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8 lg:py-20">
            <div class="flex flex-col gap-2">
                <h2 class="font-serif text-2xl font-semibold text-cream sm:text-3xl">Not sure which model fits?</h2>
                <p class="max-w-xl text-sm leading-relaxed text-body">Describe the situation and we will recommend the right structure. The first conversation is always confidential.</p>
            </div>
            <div class="flex flex-wrap gap-4">
                <x-button variant="primary" href="{{ route('inquiries.create') }}">
                    Start a Confidential Inquiry
                    <x-icon name="lock" class="h-3.5 w-3.5" />
                </x-button>
                <x-button variant="secondary" href="{{ route('engagement-models.index') }}">
                    All Models
                    <x-icon name="chevron-right" class="h-3.5 w-3.5" />
                </x-button>
            </div>
        </div>
    </section>
</x-layout>
