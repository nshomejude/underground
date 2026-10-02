<x-admin.shell title="New Plan" max-width="max-w-2xl">
    <form method="POST" action="{{ route('admin.plans.store') }}" class="flex flex-col gap-6 rounded-adm border border-hairline bg-surface px-5 py-6 shadow-adm-xs sm:px-8 sm:py-8">
        @csrf

        @include('admin.plans._form')

        <div class="flex items-center gap-4 pt-2">
            <x-button variant="primary" type="submit">Create Plan</x-button>
            <a href="{{ route('admin.plans.index') }}" class="text-xs font-semibold uppercase tracking-wider text-muted hover:text-cream">Cancel</a>
        </div>
    </form>
</x-admin.shell>
