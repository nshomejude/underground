<x-layout :title="$engagementModel->name">
    <article class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
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
        </div>

        <div class="mt-10 border-t border-border pt-10">
            <p class="text-base leading-relaxed text-body">
                {{ $engagementModel->summary }}
            </p>
        </div>

        <div class="mt-12 flex flex-wrap items-center gap-4">
            <x-button variant="secondary" href="{{ route('engagement-models.index') }}">
                Explore All Engagement Models
                <x-icon name="chevron-right" class="h-3.5 w-3.5" />
            </x-button>
        </div>
    </article>
</x-layout>
