<x-layout title="Team">
    <section class="mx-auto flex max-w-6xl flex-col gap-12 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading eyebrow="Leadership">
            The Partners
        </x-section-heading>

        <p class="max-w-2xl text-base leading-relaxed text-body">
            A deliberately small group of principals, each with their own standing before they ever
            joined the firm. Every mandate is led by one of them, by name.
        </p>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($leaders as $leader)
                <div class="flex flex-col items-start gap-5 border border-border bg-surface p-8">
                    @if (!empty($leader['portrait']))
                        <div class="h-20 w-20 overflow-hidden rounded-full border border-gold">
                            <img
                                src="{{ $founderPortraitSrc }}"
                                alt="Portrait of {{ $leader['name'] }}"
                                class="h-full w-full object-cover"
                                loading="lazy"
                            >
                        </div>
                    @else
                        <span class="flex h-20 w-20 items-center justify-center rounded-full border border-gold bg-surface">
                            <x-icon name="{{ $leader['icon'] }}" class="h-8 w-8 text-gold" />
                        </span>
                    @endif

                    <div class="flex flex-col gap-2">
                        <h3 class="font-serif text-xl font-semibold leading-snug text-cream">
                            {{ $leader['name'] }}
                        </h3>
                        <p class="text-xs font-semibold uppercase tracking-widest text-gold">
                            {{ $leader['title'] }}
                        </p>
                        <p class="text-sm leading-relaxed text-body">{{ $leader['background'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</x-layout>
