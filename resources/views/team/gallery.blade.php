<x-layout title="Gallery">
    <section class="mx-auto flex max-w-6xl flex-col gap-10 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading tag="h1" eyebrow="Team">
            Gallery
        </x-section-heading>

        <p class="max-w-2xl text-base leading-relaxed text-body">
            A record of the firm's principals at work. Every photo here carries a stated basis for
            its use — we do not publish one without it.
        </p>

        @if (count($photos) > 0)
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($photos as $photo)
                    <figure class="flex flex-col gap-4 border border-border bg-surface p-6">
                        @if ($photo['image_url'])
                            <img
                                src="{{ $photo['image_url'] }}"
                                alt="{{ $photo['caption'] }}"
                                class="aspect-[4/3] w-full border border-border object-cover"
                                loading="lazy"
                            >
                        @else
                            {{-- No stock-photo placeholders: a photo pending upload gets a
                                 plain, honest placeholder tile instead of a fake image. --}}
                            <div class="flex aspect-[4/3] w-full flex-col items-center justify-center gap-2 border border-dashed border-border text-muted">
                                <x-icon name="camera" class="h-8 w-8" />
                                <span class="text-[11px] font-semibold uppercase tracking-wide">Photo Pending</span>
                            </div>
                        @endif

                        <figcaption class="flex flex-col gap-2">
                            <p class="font-serif text-lg font-semibold leading-snug text-cream">{{ $photo['caption'] }}</p>

                            @if ($photo['captured_on'])
                                <p class="inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wide text-gold">
                                    <x-icon name="clock" class="h-3.5 w-3.5" />
                                    {{ $photo['captured_on'] }}
                                </p>
                            @endif

                            @if ($photo['context'])
                                <p class="text-sm leading-relaxed text-body">{{ $photo['context'] }}</p>
                            @endif

                            <p class="mt-1 flex items-start gap-1.5 border-t border-border pt-2 text-[11px] leading-relaxed text-muted">
                                <x-icon name="lock" class="mt-0.5 h-3 w-3 shrink-0" />
                                {{ $photo['photo_rights_note'] }}
                            </p>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center gap-4 border border-dashed border-border px-8 py-20 text-center">
                <span class="flex h-16 w-16 items-center justify-center rounded-full border border-border text-muted">
                    <x-icon name="camera" class="h-7 w-7" />
                </span>
                <p class="max-w-md text-sm leading-relaxed text-muted">
                    No photos are published here yet. We only publish photos with a clear basis for
                    their use, and add them as they become available — not stock imagery in their
                    place.
                </p>
            </div>
        @endif
    </section>

    <x-cta-band tone="ink" heading="Looking for the team?" text="Meet the partners, or see the standards every mandate is held to.">
        <x-button variant="secondary" href="{{ route('team') }}">
            View Team Roster
            <x-icon name="chevron-right" class="h-3.5 w-3.5" />
        </x-button>
    </x-cta-band>
</x-layout>
