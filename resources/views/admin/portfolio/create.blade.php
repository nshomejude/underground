<x-admin.shell title="New Engagement" max-width="max-w-2xl">
    <form method="POST" action="{{ route('admin.portfolio.store') }}" class="flex flex-col gap-6 rounded-adm border border-hairline bg-surface px-5 py-6 shadow-adm-xs sm:px-8 sm:py-8">
        @csrf

        <x-admin.field name="title" label="Title" placeholder="Repositioning a Ministry Ahead of..." />
        <x-admin.field name="slug" label="Slug" placeholder="repositioning-a-ministry" />
        <x-admin.field name="sector" label="Sector" placeholder="Government & Public Sector" />
        <x-admin.textarea-field name="summary" label="Summary / Context" rows="4" />
        <x-admin.field name="outcome" label="Outcome / Demonstrable Result" placeholder="Policy mandate retained across a full change of government." />
        <x-admin.field name="real_role" label="Real Role" :required="false" placeholder="Retained strategic advisor to the ministry." />
        <x-admin.textarea-field name="deliverables" label="Deliverables (one per line)" rows="3" :required="false" />
        <x-admin.field name="period" label="Period" :required="false" placeholder="18 months" />
        <x-admin.textarea-field name="publication_agreement_note" label="Publication Agreement Note" rows="2" :required="false" />
        <x-admin.field name="position" label="Position" type="number" value="0" />

        <div class="flex items-center gap-4 pt-2">
            <x-button variant="primary" type="submit">Create Engagement</x-button>
            <a href="{{ route('admin.portfolio.index') }}" class="text-xs font-semibold uppercase tracking-wider text-muted hover:text-cream">Cancel</a>
        </div>
    </form>
</x-admin.shell>
