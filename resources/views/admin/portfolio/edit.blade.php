<x-admin.shell title="Edit Engagement" max-width="max-w-2xl">
    <form method="POST" action="{{ route('admin.portfolio.update', $engagement->slug->value) }}" class="flex flex-col gap-6 border border-border bg-surface px-6 py-8 sm:px-10 sm:py-10">
        @csrf
        @method('PUT')

        <x-admin.field name="title" label="Title" :value="$engagement->title" />
        <x-admin.field name="slug" label="Slug" :value="$engagement->slug->value" />
        <x-admin.field name="sector" label="Sector" :value="$engagement->sector" />
        <x-admin.textarea-field name="summary" label="Summary" rows="4" :value="$engagement->summary" />
        <x-admin.field name="outcome" label="Outcome" :value="$engagement->outcome" />
        <x-admin.field name="position" label="Position" type="number" :value="$engagement->position" />

        <div class="flex items-center gap-4 pt-2">
            <x-button variant="primary" type="submit">Save Changes</x-button>
            <a href="{{ route('admin.portfolio.index') }}" class="text-xs font-semibold uppercase tracking-wider text-muted hover:text-cream">Cancel</a>
        </div>
    </form>
</x-admin.shell>
