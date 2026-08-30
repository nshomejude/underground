<x-layout title="Membership">
    <section class="mx-auto flex max-w-6xl flex-col gap-12 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading eyebrow="By Invitation and Application">
            Membership
        </x-section-heading>

        <p class="max-w-2xl text-base leading-relaxed text-body">
            Underground extends three vetted tiers to governments, principals, and corporate
            institutions. There is no public checkout &mdash; every application is reviewed by a partner
            before a tier is granted.
        </p>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($tiers as $tier)
                <div class="flex flex-col gap-6 border border-border bg-surface p-8">
                    <span class="flex h-12 w-12 items-center justify-center border border-gold/40 text-gold">
                        <x-icon :name="$tier->icon" class="h-6 w-6" />
                    </span>

                    <div class="flex flex-col gap-2">
                        <h3 class="font-serif text-2xl font-semibold text-cream">{{ $tier->name }}</h3>
                        <p class="text-sm leading-relaxed text-body">{{ $tier->audience }}</p>
                    </div>

                    <x-button variant="secondary" href="{{ route('membership.apply', ['tier' => $tier->slug->value]) }}" class="mt-auto w-fit">
                        Apply
                        <x-icon name="chevron-right" class="h-3.5 w-3.5" />
                    </x-button>
                </div>
            @endforeach
        </div>
    </section>
</x-layout>
