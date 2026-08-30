<x-admin.shell title="Team">
    <div class="flex items-center justify-end">
        <x-button variant="primary" href="{{ route('admin.team.create') }}">
            <x-icon name="chevron-right" class="h-3.5 w-3.5 rotate-[-45deg]" />
            New Team Member
        </x-button>
    </div>

    <div class="overflow-x-auto border border-border">
        <table class="w-full min-w-[720px] border-collapse text-left text-sm">
            <thead>
                <tr class="border-b border-border bg-surface text-xs uppercase tracking-wider text-muted">
                    <th class="px-4 py-3 font-semibold">Position</th>
                    <th class="px-4 py-3 font-semibold">Name</th>
                    <th class="px-4 py-3 font-semibold">Title</th>
                    <th class="px-4 py-3 font-semibold">Portrait / Icon</th>
                    <th class="px-4 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($members as $member)
                    <tr class="border-b border-border transition-colors last:border-b-0 hover:bg-surface-raised">
                        <td class="px-4 py-3 text-muted">{{ $member->position }}</td>
                        <td class="px-4 py-3 text-cream">{{ $member->name }}</td>
                        <td class="px-4 py-3 text-body">{{ $member->title }}</td>
                        <td class="px-4 py-3">
                            @if ($member->hasPortrait)
                                <span class="text-body">Founder portrait</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-body">
                                    <x-icon :name="$member->icon" class="h-4 w-4 text-gold" />
                                    {{ $member->icon }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-4">
                                <a href="{{ route('admin.team.edit', $member->slug->value) }}" class="text-xs font-semibold uppercase tracking-wider text-gold hover:text-gold-bright">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.team.destroy', $member->slug->value) }}" onsubmit="return confirm('Remove this team member?');">
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
                        <td colspan="5" class="px-4 py-6 text-center text-muted">No team members yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin.shell>
