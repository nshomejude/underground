<x-admin.shell title="Edit Pillar">
    <form method="POST" action="{{ route('admin.pillars.update', $pillar->slug->value) }}" class="flex flex-col gap-6 border border-border bg-surface px-6 py-8 sm:px-10 sm:py-10">
        @csrf
        @method('PUT')

        <x-admin.field name="title" label="Title" :value="$pillar->title" />
        <x-admin.field name="slug" label="Slug" :value="$pillar->slug->value" />
        <x-admin.field name="qualifier" label="Qualifier" :value="$pillar->qualifier" />
        <x-admin.select-field name="icon" label="Icon" :options="\App\Support\IconLibrary::NAMES" :value="$pillar->icon" />
        <x-admin.field name="position" label="Position" type="number" :value="$pillar->position" />

        <div class="flex items-center gap-4 pt-2">
            <x-button variant="primary" type="submit">Save Changes</x-button>
            <a href="{{ route('admin.pillars.index') }}" class="text-xs font-semibold uppercase tracking-wider text-muted hover:text-cream">Cancel</a>
        </div>
    </form>
</x-admin.shell>
