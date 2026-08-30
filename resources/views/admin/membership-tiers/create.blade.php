<x-admin.shell title="New Membership Tier" max-width="max-w-2xl">
    <form method="POST" action="{{ route('admin.membership-tiers.store') }}" class="flex flex-col gap-6 rounded-adm border border-hairline bg-surface px-5 py-6 shadow-adm-xs sm:px-8 sm:py-8">
        @csrf

        <x-admin.field name="name" label="Name" placeholder="Sovereign Partner" />
        <x-admin.field name="slug" label="Slug" placeholder="sovereign-partner" />
        <x-admin.textarea-field name="audience" label="Audience" rows="3" />
        <x-admin.select-field name="icon" label="Icon" :options="\App\Support\IconLibrary::NAMES" />
        <x-admin.field name="position" label="Position" type="number" value="0" />

        <div class="flex items-center gap-4 pt-2">
            <x-button variant="primary" type="submit">Create Tier</x-button>
            <a href="{{ route('admin.membership-tiers.index') }}" class="text-xs font-semibold uppercase tracking-wider text-muted hover:text-cream">Cancel</a>
        </div>
    </form>
</x-admin.shell>
