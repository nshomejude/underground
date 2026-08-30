<x-admin.shell title="New Metric" max-width="max-w-2xl">
    <form method="POST" action="{{ route('admin.metrics.store') }}" class="flex flex-col gap-6 rounded-adm border border-hairline bg-surface px-5 py-6 shadow-adm-xs sm:px-8 sm:py-8">
        @csrf

        <x-admin.field name="value" label="Value" placeholder="250+" />
        <x-admin.field name="label" label="Label" placeholder="Government relationships worldwide" />
        <x-admin.field name="slug" label="Slug" placeholder="government-relationships" />
        <x-admin.select-field name="icon" label="Icon" :options="\App\Support\IconLibrary::NAMES" />
        <x-admin.field name="position" label="Position" type="number" value="0" />

        <div class="flex items-center gap-4 pt-2">
            <x-button variant="primary" type="submit">Create Metric</x-button>
            <a href="{{ route('admin.metrics.index') }}" class="text-xs font-semibold uppercase tracking-wider text-muted hover:text-cream">Cancel</a>
        </div>
    </form>
</x-admin.shell>
