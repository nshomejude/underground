<x-admin.shell title="Projects">
    <div class="flex items-center justify-end">
        <x-button variant="primary" href="{{ route('admin.projects.create') }}">
            <x-icon name="chevron-right" class="h-3.5 w-3.5 rotate-[-45deg]" />
            New Project
        </x-button>
    </div>

    <div class="overflow-x-auto border border-border">
        <table class="w-full min-w-[720px] border-collapse text-left text-sm">
            <thead>
                <tr class="border-b border-border bg-surface text-xs uppercase tracking-wider text-muted">
                    <th class="px-4 py-3 font-semibold">Position</th>
                    <th class="px-4 py-3 font-semibold">Title</th>
                    <th class="px-4 py-3 font-semibold">Sector</th>
                    <th class="px-4 py-3 font-semibold">Icon</th>
                    <th class="px-4 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($projects as $project)
                    <tr class="border-b border-border transition-colors last:border-b-0 hover:bg-surface-raised">
                        <td class="px-4 py-3 text-muted">{{ $project->position }}</td>
                        <td class="px-4 py-3 text-cream">{{ $project->title }}</td>
                        <td class="px-4 py-3 text-body">{{ $project->sector }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1.5 text-body">
                                <x-icon :name="$project->icon" class="h-4 w-4 text-gold" />
                                {{ $project->icon }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-4">
                                <a href="{{ route('admin.projects.edit', $project->slug->value) }}" class="text-xs font-semibold uppercase tracking-wider text-gold hover:text-gold-bright">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.projects.destroy', $project->slug->value) }}" onsubmit="return confirm('Remove this project?');">
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
                        <td colspan="5" class="px-4 py-6 text-center text-muted">No projects yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin.shell>
