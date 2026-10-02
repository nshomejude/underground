<x-layout title="Membership plans" description="Compare the three Underground membership tiers: card, directory, connections, voting and forums. Pricing is by application.">
    <section class="mx-auto flex max-w-6xl flex-col gap-10 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading tag="h1" eyebrow="Membership">
            Membership plans
        </x-section-heading>

        <p class="max-w-2xl text-base leading-relaxed text-body">
            Three tiers, each with its own card. Membership is by application and reviewed personally,
            so there is no public checkout: terms are agreed with approved members.
        </p>

        <div class="pl">
            @include('plans._compare', ['mode' => 'public'])
        </div>

        <p class="max-w-2xl text-sm leading-relaxed text-body">
            Not sure which tier fits? <a href="{{ route('contact') }}" class="text-gold underline decoration-gold/40 underline-offset-4 hover:text-gold-bright">Contact us</a>
            or see <a href="{{ route('membership.index') }}" class="text-gold underline decoration-gold/40 underline-offset-4 hover:text-gold-bright">how membership works</a>.
        </p>
    </section>
</x-layout>
