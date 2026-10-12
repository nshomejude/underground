<x-admin.shell title="New Office" max-width="max-w-2xl">
    <form method="POST" action="{{ route('admin.offices.store') }}" class="flex flex-col gap-6 rounded-adm border border-hairline bg-surface px-5 py-6 shadow-adm-xs sm:px-8 sm:py-8">
        @csrf

        <x-admin.field name="name" label="Name" placeholder="Nairobi Regional Office" />
        <x-admin.field name="slug" label="Slug" placeholder="nairobi-regional-office" />
        <x-admin.field name="region" label="Region" placeholder="East Africa" />
        <x-admin.textarea-field name="address" label="Address (optional)" rows="2" :required="false" />
        <x-admin.field name="email" label="Email (optional)" type="email" :required="false" />
        <x-admin.field name="phone" label="Phone (optional)" :required="false" />
        <x-admin.textarea-field name="blurb" label="Blurb" rows="3" />

        <div class="grid grid-cols-2 gap-4">
            <x-admin.field name="lat" label="Latitude (optional)" type="number" :required="false" />
            <x-admin.field name="lng" label="Longitude (optional)" type="number" :required="false" />
        </div>

        <x-admin.textarea-field
            name="countries_text"
            label="Countries covered (one per line)"
            rows="4"
            :required="false"
            placeholder="Kenya&#10;Uganda&#10;Tanzania"
        />

        <div class="flex flex-col gap-1.5">
            <label for="status" class="text-[13px] font-medium text-body">Status</label>
            <select name="status" id="status" required class="rounded-adm border border-hairline bg-surface px-3 py-2 text-sm text-cream focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/25">
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}" @selected(old('status') === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </select>
            <p class="text-xs text-muted">Never Active Office unless Underground directly staffs this location.</p>
            @error('status')
                <p class="text-xs text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex flex-col gap-1.5">
            <label for="relationship_type" class="text-[13px] font-medium text-body">Relationship Type</label>
            <select name="relationship_type" id="relationship_type" required class="rounded-adm border border-hairline bg-surface px-3 py-2 text-sm text-cream focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/25">
                @foreach ($relationshipTypes as $type)
                    <option value="{{ $type->value }}" @selected(old('relationship_type') === $type->value)>{{ $type->label() }}</option>
                @endforeach
            </select>
            @error('relationship_type')
                <p class="text-xs text-danger">{{ $message }}</p>
            @enderror
        </div>

        <x-admin.checkbox-field name="is_headquarters" label="This is the firm's headquarters" />
        <x-admin.field name="position" label="Position" type="number" value="0" />

        <div class="flex items-center gap-4 pt-2">
            <x-button variant="primary" type="submit">Create Office</x-button>
            <a href="{{ route('admin.offices.index') }}" class="text-xs font-semibold uppercase tracking-wider text-muted hover:text-cream">Cancel</a>
        </div>
    </form>
</x-admin.shell>
