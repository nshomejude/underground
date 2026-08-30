<x-admin.shell title="Metrics">
    <div class="flex items-center justify-end">
        <x-button variant="primary" href="{{ route('admin.metrics.create') }}">
            <x-icon name="chevron-right" class="h-3.5 w-3.5 rotate-[-45deg]" />
            New Metric
        </x-button>
    </div>

    <div class="overflow-x-auto border border-border">
        <table class="w-full min-w-[640px] border-collapse text-left text-sm">
            <thead>
                <tr class="border-b border-border bg-surface text-xs uppercase tracking-wider text-muted">
                    <th class="px-4 py-3 font-semibold">Position</th>
                    <th class="px-4 py-3 font-semibold">Value</th>
                    <th class="px-4 py-3 font-semibold">Label</th>
                    <th class="px-4 py-3 font-semibold">Icon</th>
                    <th class="px-4 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($metrics as $metric)
                    <tr class="border-b border-border last:border-b-0">
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
