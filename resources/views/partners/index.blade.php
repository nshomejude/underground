<x-layout title="Partners">
    <section class="mx-auto flex max-w-6xl flex-col gap-12 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading eyebrow="Who We Work Alongside">
            Partners
        </x-section-heading>

        <p class="max-w-2xl text-base leading-relaxed text-body">
            No mandate is run alone. We work alongside a vetted circle of firms and institutions
            &mdash; identified here by category, never by name, consistent with the same discretion
            we extend to our clients.
        </p>

        <div class="grid grid-cols-1 gap-px bg-border sm:grid-cols-2">
            @foreach ($categories as $category)
                <div class="flex flex-col items-start gap-4 bg-surface p-8">
                    <span class="flex h-12 w-12 items-center justify-center border border-gold text-gold">
                        <x-icon name="{{ $category['icon'] }}" class="h-6 w-6" />
                    </span>
                    <h3 class="font-serif text-lg font-semibold leading-snug text-cream">
                        {{ $category['title'] }}
                    </h3>
                    <p class="text-sm leading-relaxed text-body">{{ $category['body'] }}</p>
                </div>
            @endforeach
        </div>
    </section>
</x-layout>
