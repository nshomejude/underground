<x-layout title="Collaboration">
    <section class="mx-auto flex max-w-6xl flex-col gap-12 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading eyebrow="How We Work Together">
            Collaboration
        </x-section-heading>

        <p class="max-w-2xl text-base leading-relaxed text-body">
            A mandate does not end at the proposal. Here is how we actually work once we are in the
            room &mdash; day to day, not just at the milestones.
        </p>

        <div class="grid grid-cols-1 gap-px bg-border sm:grid-cols-2">
            @foreach ($modes as $mode)
                <div class="flex flex-col items-start gap-4 bg-surface p-8">
                    <span class="flex h-12 w-12 items-center justify-center border border-gold text-gold">
                        <x-icon name="{{ $mode['icon'] }}" class="h-6 w-6" />
                    </span>
                    <h3 class="font-serif text-lg font-semibold leading-snug text-cream">
                        {{ $mode['title'] }}
                    </h3>
                    <p class="text-sm leading-relaxed text-body">{{ $mode['body'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="flex flex-col gap-6 border-t border-border pt-10">
            <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-gold">What Stays Constant</h2>

            <ul class="flex flex-col gap-4">
                @foreach ($principles as $principle)
                    <li class="flex items-start gap-3">
                        <x-icon name="check-circle" class="mt-0.5 h-4 w-4 shrink-0 text-gold" />
                        <span class="text-sm leading-relaxed text-body">{{ $principle }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
</x-layout>
