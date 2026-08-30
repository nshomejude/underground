<x-layout title="Membership Cards">
    <section class="mx-auto flex max-w-6xl flex-col gap-12 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading eyebrow="The Physical Artifact of Standing">
            Membership Cards
        </x-section-heading>

        <p class="max-w-2xl text-base leading-relaxed text-body">
            Every application that clears review is issued a permanent Underground membership
            card &mdash; carried, never advertised. Shown below is one illustrative card per
            vetted tier. Select "View Back" on a card to see its verification face.
        </p>

        <div class="grid gap-16 sm:grid-cols-1 lg:grid-cols-3 lg:gap-10">
            @foreach ($samples as $sample)
                <x-membership-card
                    :variant="$sample['variant']"
                    :name="$sample['name']"
                    :representative="$sample['representative']"
                    :representative-title="$sample['representativeTitle']"
                    :tier="$sample['tier']"
                    :member-id="$sample['memberId']"
                    :issued-on="$sample['issuedOn']"
                    :valid-through="$sample['validThrough']"
                />
            @endforeach
        </div>

        <p class="max-w-2xl text-xs leading-relaxed text-muted">
            Illustrative data only. No application on this page corresponds to a real member,
            institution, or government &mdash; see <a href="{{ route('membership.index') }}" class="text-gold underline decoration-gold/40 underline-offset-4 hover:text-gold-bright">Membership</a>
            to apply for one of the three vetted tiers.
        </p>
    </section>
</x-layout>
