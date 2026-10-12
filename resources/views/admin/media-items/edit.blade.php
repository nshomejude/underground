<x-admin.shell title="Edit Media Item" max-width="max-w-2xl">
    <form method="POST" action="{{ route('admin.media-items.update', $mediaItem->slug->value) }}" class="flex flex-col gap-6 rounded-adm border border-hairline bg-surface px-5 py-6 shadow-adm-xs sm:px-8 sm:py-8">
        @csrf
        @method('PUT')

        <div class="flex flex-col gap-1.5">
            <label for="type" class="text-[13px] font-medium text-body">Type</label>
            <select name="type" id="type" required class="rounded-adm border border-hairline bg-surface px-3 py-2 text-sm text-cream focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/25">
                @foreach (\Domain\Content\ValueObjects\MediaType::cases() as $type)
                    <option value="{{ $type->value }}" @selected(old('type', $mediaItem->type->value) === $type->value)>{{ $type->label() }}</option>
                @endforeach
            </select>
            @error('type')
                <p class="text-xs text-danger">{{ $message }}</p>
            @enderror
        </div>

        <x-admin.field name="title" label="Title" :value="$mediaItem->title" />
        <x-admin.field name="slug" label="Slug" :value="$mediaItem->slug->value" />
        <x-admin.textarea-field name="description" label="Description" rows="4" :value="$mediaItem->description" />
        <x-admin.field name="date" label="Date" type="date" :value="$mediaItem->date?->toDateString()" :required="false" />
        <x-admin.field name="speaker" label="Speaker / Contributor" :value="$mediaItem->speaker" :required="false" />

        <div class="flex flex-col gap-1.5">
            <label for="status" class="text-[13px] font-medium text-body">Status</label>
            <select name="status" id="status" required class="rounded-adm border border-hairline bg-surface px-3 py-2 text-sm text-cream focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/25">
                @foreach (\Domain\Content\ValueObjects\MediaItemStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected(old('status', $mediaItem->status->value) === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </select>
            @error('status')
                <p class="text-xs text-danger">{{ $message }}</p>
            @enderror
        </div>

        <x-admin.field name="position" label="Position" type="number" :value="$mediaItem->position" />

        <div class="flex items-center gap-4 pt-2">
            <x-button variant="primary" type="submit">Save Changes</x-button>
            <a href="{{ route('admin.media-items.index') }}" class="text-xs font-semibold uppercase tracking-wider text-muted hover:text-cream">Cancel</a>
        </div>
    </form>
</x-admin.shell>
