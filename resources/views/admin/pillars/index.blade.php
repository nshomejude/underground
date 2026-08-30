<x-admin.shell title="Pillars">
    <div class="flex items-center justify-end">
        <x-button variant="primary" href="{{ route('admin.pillars.create') }}">
            <x-icon name="chevron-right" class="h-3.5 w-3.5 rotate-[-45deg]" />
            New Pillar
        </x-button>
    </div>

    <div class="overflow-x-auto border border-border">
        <table class="w-full min-w-[640px] border-collapse text-left text-sm">
            <thead>
                <tr class="border-b border-border bg-surface text-xs uppercase tracking-wider text-muted">
                    <th class="px-4 py-3 font-semibold">Position</th>
                    <th class="px-4 py-3 font-semibold">Title</th>
                    <th class="px-4 py-3 font-semibold">Qualifier</th>
                    <th class="px-4 py-3 font-semibold">Icon</th>
                    <th class="px-4 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pillars as $pillar)
                    <tr class="border-b border-border last:border-b-0">
                        <td class="px-4 py-3 text-muted">{{ $pillar->position }}</td>
                        <td class="px-4 py-3 text-cream">{{ $pillar->title }}</td>
                        <td class="px-4 py-3 text-body">{{ $pillar->qualifier }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1.5 text-body">
                                <x-icon :name="$pillar->icon" class="h-4 w-4 text-gold" />
                                {{ $pillar->icon }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-4">
                                <a href="{{ route('admin.pillars.edit', $pillar->slug->value) }}" class="text-xs font-semibold uppercase tracking-wider text-gold hover:text-gold-bright">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.pillars.destroy', $pillar->slug->value) }}" onsubmit="return confirm('Remove this pillar?');">
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
                        <td colspan="5" class="px-4 py-6 text-center text-muted">No pillars yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin.shell>
