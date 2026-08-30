<x-admin.shell title="Insights">
        <x-slot:actions>
            <x-button variant="primary" href="{{ route('admin.insights.create') }}" class="w-fit">
                <x-icon name="newspaper" class="h-3.5 w-3.5" />
                New Insight
            </x-button>
        </x-slot:actions>

        <div class="overflow-x-auto rounded-adm border border-hairline bg-surface shadow-adm-xs">
            <table class="w-full min-w-[640px] text-left text-sm">
                <thead class="border-b border-hairline bg-surface-raised/40 text-[11px] font-medium uppercase tracking-wider text-muted">
                    <tr>
                        <th class="px-4 py-3">Title</th>
                        <th class="px-4 py-3">Category</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Published</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline">
                    @forelse ($insights as $insight)
                        <tr class="transition-colors hover:bg-surface-raised">
                            <td class="px-4 py-3 text-cream">
                                {{ $insight->title }}
                                <div class="text-xs text-muted">{{ $insight->slug->value }}</div>
                            </td>
                            <td class="px-4 py-3 text-body">{{ $insight->category }}</td>
                            <td class="px-4 py-3">
                                @if ($insight->isPublished())
                                    <x-status-badge label="Published" tone="success" />
                                @else
                                    <x-status-badge label="Draft" tone="neutral" />
                                @endif
                            </td>
                            <td class="px-4 py-3 text-body">
                                {{ $insight->publishedAt?->format('j M Y') ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('admin.insights.edit', $insight->slug->value) }}" class="text-xs font-semibold uppercase tracking-wider text-gold hover:text-gold-bright">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.insights.destroy', $insight->slug->value) }}" onsubmit="return confirm('Delete this insight? This cannot be undone.');">
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
                                No insights yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
</x-admin.shell>
