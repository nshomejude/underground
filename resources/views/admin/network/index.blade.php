<x-admin.shell title="Network">
    <dl class="mb-4 grid gap-3 text-sm sm:grid-cols-3 lg:grid-cols-6">
        @foreach ([['Total', $counts['total']], ['Pending', $counts['pending']], ['Accepted', $counts['accepted']], ['Declined', $counts['declined']], ['Blocked', $counts['blocked']], ['Collaborations', $collaborations]] as [$label, $value])
            <div class="rounded-adm border border-hairline bg-surface px-4 py-3 shadow-adm-xs">
                <dt class="text-[11px] uppercase tracking-wider text-muted">{{ $label }}</dt>
                <dd class="text-lg text-cream">{{ $value }}</dd>
            </div>
        @endforeach
    </dl>
    <p class="mb-3 text-xs text-body">{{ $last7 }} new in the last 7 days.</p>

    <nav class="mb-4 flex flex-wrap gap-2" aria-label="Filter by status">
        @foreach (['all' => 'All', 'pending' => 'Pending', 'accepted' => 'Accepted', 'declined' => 'Declined', 'blocked' => 'Blocked'] as $s => $label)
            <a href="{{ route('admin.network.index', $s === 'all' ? [] : ['status' => $s]) }}"
               class="rounded-adm border px-3 py-1.5 text-xs font-semibold uppercase tracking-wider {{ ($status ?? 'all') === $s ? 'border-gold text-gold' : 'border-hairline text-body hover:text-cream' }}">{{ $label }}</a>
        @endforeach
    </nav>

    <div class="overflow-x-auto rounded-adm border border-hairline bg-surface shadow-adm-xs">
        <table class="w-full min-w-[720px] border-collapse text-left text-sm">
            <thead>
                <tr class="border-b border-hairline bg-surface-raised/40 text-[11px] font-medium uppercase tracking-wider text-muted">
                    <th class="px-4 py-3 font-semibold">Date</th>
                    <th class="px-4 py-3 font-semibold">Requester</th>
                    <th class="px-4 py-3 font-semibold">Addressee</th>
                    <th class="px-4 py-3 font-semibold">Kind</th>
                    <th class="px-4 py-3 font-semibold">Status</th>
                    <th class="px-4 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($connections as $c)
                    <tr class="border-b border-hairline last:border-b-0 hover:bg-surface-raised">
                        <td class="px-4 py-3 text-muted">{{ $c->created_at->format('j M Y') }}</td>
                        <td class="px-4 py-3 text-cream">{{ $c->requester?->name }}</td>
                        <td class="px-4 py-3 text-cream">{{ $c->addressee?->name }}</td>
                        <td class="px-4 py-3 text-body">{{ $c->isCollaboration() ? 'Collaborate' : 'Connect' }}</td>
                        <td class="px-4 py-3 text-body">{{ ucfirst($c->status) }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.network.show', $c) }}" class="text-xs font-semibold uppercase tracking-wider text-gold hover:text-gold-bright">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-muted">No connections here.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $connections->links() }}</div>
</x-admin.shell>
