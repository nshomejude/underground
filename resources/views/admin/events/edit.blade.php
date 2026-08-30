<x-admin.shell title="Edit Event">
    <form method="POST" action="{{ route('admin.events.update', $event->slug->value) }}" class="flex flex-col gap-6 border border-border bg-surface px-6 py-8 sm:px-10 sm:py-10">
        @csrf
        @method('PUT')

        <x-admin.field name="name" label="Name" :value="$event->name" />
        <x-admin.field name="slug" label="Slug" :value="$event->slug->value" />
        <x-admin.field name="date" label="Date" type="date" :value="$event->date->toDateString()" />
        <x-admin.field name="location" label="Location" :value="$event->location" />
        <x-admin.textarea-field name="description" label="Description" rows="4" :value="$event->description" />
        <x-admin.field name="position" label="Position" type="number" :value="$event->position" />

        <div class="flex items-center gap-4 pt-2">
            <x-button variant="primary" type="submit">Save Changes</x-button>
            <a href="{{ route('admin.events.index') }}" class="text-xs font-semibold uppercase tracking-wider text-muted hover:text-cream">Cancel</a>
        </div>
    </form>
</x-admin.shell>
