<x-admin.shell title="Edit Photo" max-width="max-w-2xl">
    <form method="POST" action="{{ route('admin.gallery.update', $photo->slug->value) }}" class="flex flex-col gap-6 rounded-adm border border-hairline bg-surface px-5 py-6 shadow-adm-xs sm:px-8 sm:py-8">
        @csrf
        @method('PUT')

        <x-admin.field name="caption" label="Caption" :value="$photo->caption" />
        <x-admin.field name="slug" label="Slug" :value="$photo->slug->value" />
        <x-admin.textarea-field name="context" label="Context" rows="3" :value="$photo->context" :required="false" />
        <x-admin.field name="captured_on" label="Date" :value="$photo->capturedOn" :required="false" placeholder="March 2026" />
        <x-admin.textarea-field name="photo_rights_note" label="Photo Rights Note" rows="2" :value="$photo->photoRightsNote" />
        <x-admin.field name="image_url" label="Image URL" type="url" :value="$photo->imageUrl" :required="false" placeholder="https://..." />
        <x-admin.field name="position" label="Position" type="number" :value="$photo->position" />

        <div class="flex items-center gap-4 pt-2">
            <x-button variant="primary" type="submit">Save Changes</x-button>
            <a href="{{ route('admin.gallery.index') }}" class="text-xs font-semibold uppercase tracking-wider text-muted hover:text-cream">Cancel</a>
        </div>
    </form>
</x-admin.shell>
