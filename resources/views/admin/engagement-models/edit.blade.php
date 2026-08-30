<x-admin.shell title="Edit Engagement Model">
    <form method="POST" action="{{ route('admin.engagement-models.update', $engagementModel->slug->value) }}" class="flex flex-col gap-6 border border-border bg-surface px-6 py-8 sm:px-10 sm:py-10">
        @csrf
        @method('PUT')

        <x-admin.field name="name" label="Name" :value="$engagementModel->name" />
        <x-admin.field name="slug" label="Slug" :value="$engagementModel->slug->value" />
        <x-admin.textarea-field name="summary" label="Summary" :value="$engagementModel->summary" />
        <x-admin.select-field name="icon" label="Icon" :options="\App\Support\IconLibrary::NAMES" :value="$engagementModel->icon" />
        <x-admin.field name="position" label="Position" type="number" :value="$engagementModel->position" />

        <div class="flex items-center gap-4 pt-2">
            <x-button variant="primary" type="submit">Save Changes</x-button>
            <a href="{{ route('admin.engagement-models.index') }}" class="text-xs font-semibold uppercase tracking-wider text-muted hover:text-cream">Cancel</a>
        </div>
    </form>
</x-admin.shell>
