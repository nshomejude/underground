<x-admin.shell title="Edit Metric" max-width="max-w-2xl">
    <form method="POST" action="{{ route('admin.metrics.update', $metric->slug->value) }}" class="flex flex-col gap-6 rounded-adm border border-hairline bg-surface px-5 py-6 shadow-adm-xs sm:px-8 sm:py-8">
        @csrf
        @method('PUT')

        <x-admin.field name="value" label="Value" :value="$metric->value" />
        <x-admin.field name="label" label="Label" :value="$metric->label" />
        <x-admin.field name="slug" label="Slug" :value="$metric->slug->value" />
        <x-admin.select-field name="icon" label="Icon" :options="\App\Support\IconLibrary::NAMES" :value="$metric->icon" />
        <x-admin.field name="position" label="Position" type="number" :value="$metric->position" />

        <div class="flex items-center gap-4 pt-2">
            <x-button variant="primary" type="submit">Save Changes</x-button>
            <a href="{{ route('admin.metrics.index') }}" class="text-xs font-semibold uppercase tracking-wider text-muted hover:text-cream">Cancel</a>
        </div>
    </form>
</x-admin.shell>
