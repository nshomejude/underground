<x-admin.shell title="New Partner Category" max-width="max-w-2xl">
    <form method="POST" action="{{ route('admin.partners.store') }}" class="flex flex-col gap-6 rounded-adm border border-hairline bg-surface px-5 py-6 shadow-adm-xs sm:px-8 sm:py-8">
        @csrf

        <x-admin.field name="title" label="Title" placeholder="Advisory Firms" />
        <x-admin.field name="slug" label="Slug" placeholder="advisory-firms" />
        <x-admin.select-field name="icon" label="Icon" :options="\App\Support\IconLibrary::NAMES" />
        <x-admin.textarea-field name="body" label="Body" rows="4" />
        <x-admin.field name="position" label="Position" type="number" value="0" />

        <div class="flex items-center gap-4 pt-2">
            <x-button variant="primary" type="submit">Create Partner Category</x-button>
            <a href="{{ route('admin.partners.index') }}" class="text-xs font-semibold uppercase tracking-wider text-muted hover:text-cream">Cancel</a>
        </div>
    </form>
</x-admin.shell>
