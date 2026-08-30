<x-admin.shell title="Edit Membership Tier" max-width="max-w-2xl">
    <form method="POST" action="{{ route('admin.membership-tiers.update', $tier->slug->value) }}" class="flex flex-col gap-6 rounded-adm border border-hairline bg-surface px-5 py-6 shadow-adm-xs sm:px-8 sm:py-8">
        @csrf
        @method('PUT')

        <x-admin.field name="name" label="Name" :value="$tier->name" />
        <x-admin.field name="slug" label="Slug" :value="$tier->slug->value" />
        <x-admin.textarea-field name="audience" label="Audience" rows="3" :value="$tier->audience" />
        <x-admin.select-field name="icon" label="Icon" :options="\App\Support\IconLibrary::NAMES" :value="$tier->icon" />
        <x-admin.field name="position" label="Position" type="number" :value="$tier->position" />

        <div class="flex items-center gap-4 pt-2">
            <x-button variant="primary" type="submit">Save Changes</x-button>
            <a href="{{ route('admin.membership-tiers.index') }}" class="text-xs font-semibold uppercase tracking-wider text-muted hover:text-cream">Cancel</a>
        </div>
    </form>
</x-admin.shell>
