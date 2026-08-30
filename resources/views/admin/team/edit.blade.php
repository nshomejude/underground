<x-admin.shell title="Edit Team Member" max-width="max-w-2xl">
    <form method="POST" action="{{ route('admin.team.update', $member->slug->value) }}" class="flex flex-col gap-6 rounded-adm border border-hairline bg-surface px-5 py-6 shadow-adm-xs sm:px-8 sm:py-8">
        @csrf
        @method('PUT')

        <x-admin.field name="name" label="Name" :value="$member->name" />
        <x-admin.field name="slug" label="Slug" :value="$member->slug->value" />
        <x-admin.field name="title" label="Title" :value="$member->title" />
        <x-admin.textarea-field name="background" label="Background" rows="4" :value="$member->background" />
        <x-admin.checkbox-field name="has_portrait" label="Uses the founder portrait" :checked="$member->hasPortrait" />
        <x-admin.select-field name="icon" label="Icon (ignored if founder portrait is checked)" :options="\App\Support\IconLibrary::NAMES" :value="$member->icon" :required="false" />
        <x-admin.field name="position" label="Position" type="number" :value="$member->position" />

        <div class="flex items-center gap-4 pt-2">
            <x-button variant="primary" type="submit">Save Changes</x-button>
            <a href="{{ route('admin.team.index') }}" class="text-xs font-semibold uppercase tracking-wider text-muted hover:text-cream">Cancel</a>
        </div>
    </form>
</x-admin.shell>
