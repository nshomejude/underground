<x-admin.shell title="New Sector">
    <form method="POST" action="{{ route('admin.sectors.store') }}" class="flex flex-col gap-6 border border-border bg-surface px-6 py-8 sm:px-10 sm:py-10">
        @csrf

        <x-admin.field name="name" label="Name" />
        <x-admin.field name="slug" label="Slug" placeholder="oil-gas" />
        <x-admin.textarea-field name="summary" label="Summary" />
        <x-admin.field name="motif" label="Motif" placeholder="skyline" />
        <x-admin.field name="position" label="Position" type="number" value="0" />

        <div class="flex items-center gap-4 pt-2">
            <x-button variant="primary" type="submit">Create Sector</x-button>
            <a href="{{ route('admin.sectors.index') }}" class="text-xs font-semibold uppercase tracking-wider text-muted hover:text-cream">Cancel</a>
        </div>
    </form>
</x-admin.shell>
