<x-admin.shell title="Edit Insight" max-width="max-w-2xl">
        <form method="POST" action="{{ route('admin.insights.update', $insight->slug->value) }}" class="flex flex-col gap-6 rounded-adm border border-hairline bg-surface px-5 py-6 shadow-adm-xs sm:px-8 sm:py-8">
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
</x-admin.shell>
