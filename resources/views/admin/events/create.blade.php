<x-admin.shell title="New Event" max-width="max-w-2xl">
    <form method="POST" action="{{ route('admin.events.store') }}" class="flex flex-col gap-6 border border-border bg-surface px-6 py-8 sm:px-10 sm:py-10">
        @csrf

        <x-admin.field name="name" label="Name" placeholder="Underground Winter Roundtable" />
        <x-admin.field name="slug" label="Slug" placeholder="underground-winter-roundtable" />
        <x-admin.field name="date" label="Date" type="date" />
        <x-admin.field name="location" label="Location" placeholder="Geneva, Switzerland" />
        <x-admin.textarea-field name="description" label="Description" rows="4" />
        <x-admin.field name="position" label="Position" type="number" value="0" />

        <div class="flex items-center gap-4 pt-2">
            <x-button variant="primary" type="submit">Create Event</x-button>
            <a href="{{ route('admin.events.index') }}" class="text-xs font-semibold uppercase tracking-wider text-muted hover:text-cream">Cancel</a>
        </div>
    </form>
</x-admin.shell>
