<x-admin.shell title="Metrics">
    <x-slot:actions>
        <x-button variant="primary" href="{{ route('admin.metrics.create') }}">
            <x-icon name="chevron-right" class="h-3.5 w-3.5 rotate-[-45deg]" />
            New Metric
        </x-button>
    </x-slot:actions>

    <div class="overflow-x-auto rounded-adm border border-hairline bg-surface shadow-adm-xs">
        <table class="w-full min-w-[640px] border-collapse text-left text-sm">
            <thead>
                <tr class="border-b border-hairline bg-surface-raised/40 text-[11px] font-medium uppercase tracking-wider text-muted">
                    <th class="px-4 py-3 font-semibold">Position</th>
                    <th class="px-4 py-3 font-semibold">Value</th>
                    <th class="px-4 py-3 font-semibold">Label</th>
                    <th class="px-4 py-3 font-semibold">Icon</th>
                    <th class="px-4 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($metrics as $metric)
                    <tr class="border-b border-hairline transition-colors last:border-b-0 hover:bg-surface-raised">
                        <td class="px-4 py-3 text-muted">{{ $metric->position }}</td>
                        <td class="px-4 py-3 text-cream">{{ $metric->value }}</td>
                        <td class="px-4 py-3 text-body">{{ $metric->label }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1.5 text-body">
                                <x-icon :name="$metric->icon" class="h-4 w-4 text-gold" />
                                {{ $metric->icon }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-4">
                                <a href="{{ route('admin.metrics.edit', $metric->slug->value) }}" class="text-xs font-semibold uppercase tracking-wider text-gold hover:text-gold-bright">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.metrics.destroy', $metric->slug->value) }}" onsubmit="return confirm('Remove this metric?');">
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
                        <td colspan="5" class="px-4 py-6 text-center text-muted">No metrics yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin.shell>
