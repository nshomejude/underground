<x-admin.shell title="Edit Project" max-width="max-w-2xl">
    <form method="POST" action="{{ route('admin.projects.update', $project->slug->value) }}" class="flex flex-col gap-6 border border-border bg-surface px-6 py-8 sm:px-10 sm:py-10">
        @csrf
        @method('PUT')

        <x-admin.field name="title" label="Title" :value="$project->title" />
        <x-admin.field name="slug" label="Slug" :value="$project->slug->value" />
        <x-admin.field name="sector" label="Sector" :value="$project->sector" />
        <x-admin.select-field name="icon" label="Icon" :options="\App\Support\IconLibrary::NAMES" :value="$project->icon" />
        <x-admin.textarea-field name="body" label="Body" rows="4" :value="$project->body" />
        <x-admin.field name="position" label="Position" type="number" :value="$project->position" />

        <div class="flex items-center gap-4 pt-2">
            <x-button variant="primary" type="submit">Save Changes</x-button>
            <a href="{{ route('admin.projects.index') }}" class="text-xs font-semibold uppercase tracking-wider text-muted hover:text-cream">Cancel</a>
        </div>
    </form>
</x-admin.shell>
