<x-admin.shell title="Events">
    <div class="flex items-center justify-end">
        <x-button variant="primary" href="{{ route('admin.events.create') }}">
            <x-icon name="chevron-right" class="h-3.5 w-3.5 rotate-[-45deg]" />
            New Event
        </x-button>
    </div>

    <div class="overflow-x-auto border border-border">
        <table class="w-full min-w-[720px] border-collapse text-left text-sm">
            <thead>
                <tr class="border-b border-border bg-surface text-xs uppercase tracking-wider text-muted">
                    <th class="px-4 py-3 font-semibold">Date</th>
                    <th class="px-4 py-3 font-semibold">Name</th>
                    <th class="px-4 py-3 font-semibold">Location</th>
                    <th class="px-4 py-3 font-semibold">Status</th>
                    <th class="px-4 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($events as $event)
                    <tr class="border-b border-border last:border-b-0">
                        <td class="px-4 py-3 text-muted">{{ $event->date->format('M j, Y') }}</td>
                        <td class="px-4 py-3 text-cream">{{ $event->name }}</td>
                        <td class="px-4 py-3 text-body">{{ $event->location }}</td>
                        <td class="px-4 py-3">
                            @if ($event->date->isPast())
                                <x-status-badge label="Past" tone="neutral" />
                            @else
                                <x-status-badge label="Upcoming" tone="success" />
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-4">
                                <a href="{{ route('admin.events.edit', $event->slug->value) }}" class="text-xs font-semibold uppercase tracking-wider text-gold hover:text-gold-bright">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.events.destroy', $event->slug->value) }}" onsubmit="return confirm('Remove this event?');">
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
                        <td colspan="5" class="px-4 py-6 text-center text-muted">No events yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin.shell>
