<x-admin.shell title="Edit Sector">
    <form method="POST" action="{{ route('admin.sectors.update', $sector->slug->value) }}" class="flex flex-col gap-6 border border-border bg-surface px-6 py-8 sm:px-10 sm:py-10">
        @csrf
        @method('PUT')

        <x-admin.field name="name" label="Name" :value="$sector->name" />
        <x-admin.field name="slug" label="Slug" :value="$sector->slug->value" />
        <x-admin.textarea-field name="summary" label="Summary" :value="$sector->summary" />
        <x-admin.field name="motif" label="Motif" :value="$sector->motif" />
        <x-admin.field name="position" label="Position" type="number" :value="$sector->position" />

        <div class="flex items-center gap-4 pt-2">
            <x-button variant="primary" type="submit">Save Changes</x-button>
            <a href="{{ route('admin.sectors.index') }}" class="text-xs font-semibold uppercase tracking-wider text-muted hover:text-cream">Cancel</a>
        </div>
    </form>
</x-admin.shell>
