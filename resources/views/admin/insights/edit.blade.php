<x-layout title="Admin · Edit Insight">
    <section class="mx-auto flex max-w-2xl flex-col gap-8 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <x-section-heading eyebrow="Content Admin">Edit Insight</x-section-heading>

        <form method="POST" action="{{ route('admin.insights.update', $insight->slug->value) }}" class="flex flex-col gap-6">
            @csrf
            @method('PUT')
            @include('admin.insights._form', ['insight' => $insight])

            <div class="flex flex-wrap items-center gap-4 pt-2">
                <x-button type="submit" variant="primary">Save Changes</x-button>
                <a href="{{ route('admin.insights.index') }}" class="text-xs font-semibold uppercase tracking-widest text-muted hover:text-gold">
                    Cancel
                </a>
            </div>
        </form>
    </section>
</x-layout>
