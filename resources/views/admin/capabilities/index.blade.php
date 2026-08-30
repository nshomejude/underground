<x-admin.shell title="Capabilities">
        <x-slot:actions>
            <x-button variant="primary" href="{{ route('admin.capabilities.create') }}" class="w-fit">
                <x-icon name="landmark" class="h-3.5 w-3.5" />
                New Capability
            </x-button>
        </x-slot:actions>

        <div class="overflow-x-auto rounded-adm border border-hairline bg-surface shadow-adm-xs">
            <table class="w-full min-w-[640px] text-left text-sm">
                <thead class="border-b border-hairline bg-surface-raised/40 text-[11px] font-medium uppercase tracking-wider text-muted">
                    <tr>
                        <th class="px-4 py-3">Title</th>
                        <th class="px-4 py-3">Icon</th>
                        <th class="px-4 py-3">Position</th>
                        <th class="px-4 py-3">Featured</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline">
                    @forelse ($capabilities as $capability)
                        <tr class="transition-colors hover:bg-surface-raised">
                            <td class="px-4 py-3 text-cream">
                                {{ $capability->title }}
                                <div class="text-xs text-muted">{{ $capability->slug->value }}</div>
                            </td>
                            <td class="px-4 py-3 text-body">
                                <x-icon :name="$capability->icon" class="h-4 w-4 text-gold" />
                            </td>
                            <td class="px-4 py-3 text-body">{{ $capability->position }}</td>
                            <td class="px-4 py-3">
                                @if ($capability->isFeatured)
                                    <x-status-badge label="Featured" tone="success" />
                                @else
                                    <x-status-badge label="Standard" tone="neutral" />
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('admin.capabilities.edit', $capability->slug->value) }}" class="text-xs font-semibold uppercase tracking-wider text-gold hover:text-gold-bright">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.capabilities.destroy', $capability->slug->value) }}" onsubmit="return confirm('Delete this capability? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold uppercase tracking-wider text-danger hover:text-danger/80">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-muted">
                                No capabilities yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
</x-admin.shell>
