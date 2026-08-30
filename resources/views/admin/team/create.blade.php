<x-admin.shell title="New Team Member" max-width="max-w-2xl">
    <form method="POST" action="{{ route('admin.team.store') }}" class="flex flex-col gap-6 rounded-adm border border-hairline bg-surface px-5 py-6 shadow-adm-xs sm:px-8 sm:py-8">
        @csrf

        <x-admin.field name="name" label="Name" placeholder="Jane Doe" />
        <x-admin.field name="slug" label="Slug" placeholder="jane-doe" />
        <x-admin.field name="title" label="Title" placeholder="Partner, Something Important" />
        <x-admin.textarea-field name="background" label="Background" rows="4" />
        <x-admin.checkbox-field name="has_portrait" label="Uses the founder portrait" />
        <x-admin.select-field name="icon" label="Icon (ignored if founder portrait is checked)" :options="\App\Support\IconLibrary::NAMES" :required="false" />
        <x-admin.field name="position" label="Position" type="number" value="0" />

        <div class="flex items-center gap-4 pt-2">
            <x-button variant="primary" type="submit">Create Team Member</x-button>
            <a href="{{ route('admin.team.index') }}" class="text-xs font-semibold uppercase tracking-wider text-muted hover:text-cream">Cancel</a>
        </div>
    </form>
</x-admin.shell>
