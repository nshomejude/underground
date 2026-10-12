<x-admin.shell title="Offices">
    <x-slot:actions>
        <x-button variant="primary" href="{{ route('admin.offices.create') }}">
            <x-icon name="chevron-right" class="h-3.5 w-3.5 rotate-[-45deg]" />
            New Office
        </x-button>
    </x-slot:actions>

    <div class="overflow-x-auto rounded-adm border border-hairline bg-surface shadow-adm-xs">
        <table class="w-full min-w-[920px] border-collapse text-left text-sm">
            <thead>
                <tr class="border-b border-hairline bg-surface-raised/40 text-[11px] font-medium uppercase tracking-wider text-muted">
                    <th class="px-4 py-3 font-semibold">Position</th>
                    <th class="px-4 py-3 font-semibold">Name</th>
                    <th class="px-4 py-3 font-semibold">Region</th>
                    <th class="px-4 py-3 font-semibold">Status</th>
                    <th class="px-4 py-3 font-semibold">Relationship</th>
                    <th class="px-4 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($offices as $office)
                    <tr class="border-b border-hairline transition-colors last:border-b-0 hover:bg-surface-raised">
                        <td class="px-4 py-3 text-muted">{{ $office->position }}</td>
                        <td class="px-4 py-3 text-cream">
                            {{ $office->name }}
                            @if ($office->isHeadquarters)
                                <span class="ml-1.5 text-[10px] font-semibold uppercase tracking-wider text-gold">HQ</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-body">{{ $office->region }}</td>
                        <td class="px-4 py-3 text-body">{{ $office->status->label() }}</td>
                        <td class="px-4 py-3 text-body">{{ $office->relationshipType->label() }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-4">
                                <a href="{{ route('admin.offices.edit', $office->slug->value) }}" class="text-xs font-semibold uppercase tracking-wider text-gold hover:text-gold-bright">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.offices.destroy', $office->slug->value) }}" onsubmit="return confirm('Remove this office?');">
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
                        <td colspan="6" class="px-4 py-6 text-center text-muted">No offices yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin.shell>
