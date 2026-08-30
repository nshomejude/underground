<x-layout title="Admin · Capabilities">
    <section class="mx-auto flex max-w-5xl flex-col gap-8 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
            <x-section-heading eyebrow="Content Admin">Capabilities</x-section-heading>

            <x-button variant="primary" href="{{ route('admin.capabilities.create') }}" class="w-fit">
                <x-icon name="landmark" class="h-3.5 w-3.5" />
                New Capability
            </x-button>
        </div>

        @if (session('status'))
            <x-status-badge :label="session('status')" tone="success" class="w-fit" />
        @endif

        <div class="flex flex-wrap gap-3 text-xs font-semibold uppercase tracking-widest">
            <a href="{{ route('admin.insights.index') }}" class="text-muted hover:text-gold">Insights</a>
            <span class="text-muted">/</span>
            <a href="{{ route('admin.capabilities.index') }}" class="text-gold-bright">Capabilities</a>
        </div>

        <div class="overflow-x-auto border border-border bg-surface">
            <table class="w-full min-w-[640px] text-left text-sm">
                <thead class="border-b border-border text-xs uppercase tracking-wider text-muted">
                    <tr>
                        <th class="px-4 py-3">Title</th>
                        <th class="px-4 py-3">Icon</th>
                        <th class="px-4 py-3">Position</th>
                        <th class="px-4 py-3">Featured</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($capabilities as $capability)
                        <tr>
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
    </section>
</x-layout>
