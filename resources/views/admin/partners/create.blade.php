<x-admin.shell title="New Partner Category">
    <form method="POST" action="{{ route('admin.partners.store') }}" class="flex flex-col gap-6 border border-border bg-surface px-6 py-8 sm:px-10 sm:py-10">
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
