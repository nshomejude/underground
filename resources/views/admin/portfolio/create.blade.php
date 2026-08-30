<x-admin.shell title="New Engagement">
    <form method="POST" action="{{ route('admin.portfolio.store') }}" class="flex flex-col gap-6 border border-border bg-surface px-6 py-8 sm:px-10 sm:py-10">
        @csrf

        <x-admin.field name="title" label="Title" placeholder="Repositioning a Ministry Ahead of..." />
        <x-admin.field name="slug" label="Slug" placeholder="repositioning-a-ministry" />
        <x-admin.field name="sector" label="Sector" placeholder="Government & Public Sector" />
        <x-admin.textarea-field name="summary" label="Summary" rows="4" />
        <x-admin.field name="outcome" label="Outcome" placeholder="Policy mandate retained across a full change of government." />
        <x-admin.field name="position" label="Position" type="number" value="0" />

        <div class="flex items-center gap-4 pt-2">
            <x-button variant="primary" type="submit">Create Engagement</x-button>
            <a href="{{ route('admin.portfolio.index') }}" class="text-xs font-semibold uppercase tracking-wider text-muted hover:text-cream">Cancel</a>
        </div>
    </form>
</x-admin.shell>
