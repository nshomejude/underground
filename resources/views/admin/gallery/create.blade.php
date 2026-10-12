<x-admin.shell title="New Photo" max-width="max-w-2xl">
    <form method="POST" action="{{ route('admin.gallery.store') }}" class="flex flex-col gap-6 rounded-adm border border-hairline bg-surface px-5 py-6 shadow-adm-xs sm:px-8 sm:py-8">
        @csrf

        <x-admin.field name="caption" label="Caption" placeholder="Partners meeting with a delegation in Washington" />
        <x-admin.field name="slug" label="Slug" placeholder="partners-meeting-delegation" />
        <x-admin.textarea-field name="context" label="Context" rows="3" :required="false" />
        <x-admin.field name="captured_on" label="Date" :required="false" placeholder="March 2026" />
        <x-admin.textarea-field name="photo_rights_note" label="Photo Rights Note" rows="2" placeholder="Consent given by all pictured; cleared for site use." />
        <x-admin.field name="image_url" label="Image URL" type="url" :required="false" placeholder="https://..." />
        <x-admin.field name="position" label="Position" type="number" value="0" />

        <div class="flex items-center gap-4 pt-2">
            <x-button variant="primary" type="submit">Create Photo</x-button>
            <a href="{{ route('admin.gallery.index') }}" class="text-xs font-semibold uppercase tracking-wider text-muted hover:text-cream">Cancel</a>
        </div>
    </form>
</x-admin.shell>
