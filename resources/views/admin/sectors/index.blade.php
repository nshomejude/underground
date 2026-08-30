<x-admin.shell title="Sectors">
    <div class="flex items-center justify-end">
        <x-button variant="primary" href="{{ route('admin.sectors.create') }}">
            <x-icon name="chevron-right" class="h-3.5 w-3.5 rotate-[-45deg]" />
            New Sector
        </x-button>
    </div>

    <div class="overflow-x-auto border border-border">
        <table class="w-full min-w-[640px] border-collapse text-left text-sm">
            <thead>
                <tr class="border-b border-border bg-surface text-xs uppercase tracking-wider text-muted">
                    <th class="px-4 py-3 font-semibold">Position</th>
                    <th class="px-4 py-3 font-semibold">Name</th>
                    <th class="px-4 py-3 font-semibold">Slug</th>
                    <th class="px-4 py-3 font-semibold">Motif</th>
                    <th class="px-4 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sectors as $sector)
                    <tr class="border-b border-border last:border-b-0">
                        <td class="px-4 py-3 text-muted">{{ $sector->position }}</td>
                        <td class="px-4 py-3 text-cream">{{ $sector->name }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-body">{{ $sector->slug->value }}</td>
                        <td class="px-4 py-3 text-body">{{ $sector->motif }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-4">
                                <a href="{{ route('admin.sectors.edit', $sector->slug->value) }}" class="text-xs font-semibold uppercase tracking-wider text-gold hover:text-gold-bright">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.sectors.destroy', $sector->slug->value) }}" onsubmit="return confirm('Remove this sector?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold uppercase tracking-wider text-danger hover:opacity-80">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-muted">No sectors yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin.shell>
