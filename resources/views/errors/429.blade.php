<x-layout title="Too Many Requests">
    <section class="mx-auto flex min-h-[60vh] max-w-3xl flex-col items-center justify-center gap-8 px-4 py-16 text-center sm:px-6 lg:px-8 lg:py-24">
        <span class="flex h-16 w-16 items-center justify-center border border-gold text-gold">
            <x-icon name="clock" class="h-8 w-8" />
        </span>

        <div class="flex flex-col items-center gap-4">
            <x-status-badge label="429 — Too Many Requests" tone="warning" />

            <h1 class="font-serif text-3xl font-semibold leading-tight text-cream sm:text-4xl">
                Slow Down a Moment
            </h1>

            <p class="max-w-xl text-base leading-relaxed text-body">
                You've made too many requests in a short span. Please wait a minute and try
                again.
            </p>
        </div>

        <div class="flex flex-wrap items-center justify-center gap-4">
            <x-button href="{{ route('home') }}">
                Return Home
                <x-icon name="arrow-right" class="h-3.5 w-3.5" />
            </x-button>
        </div>
    </section>
</x-layout>
