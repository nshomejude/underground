<x-admin.shell title="Plan Requests">
    <nav class="mb-4 flex flex-wrap gap-2" aria-label="Filter by status">
        @foreach (['pending', 'approved', 'declined', 'withdrawn', 'all'] as $s)
            <a href="{{ route('admin.plan-requests.index', ['status' => $s]) }}"
               class="rounded-adm border px-3 py-1.5 text-xs font-semibold uppercase tracking-wider {{ $status === $s ? 'border-gold text-gold' : 'border-hairline text-body hover:text-cream' }}">{{ ucfirst($s) }}</a>
        @endforeach
    </nav>

    <div class="overflow-x-auto rounded-adm border border-hairline bg-surface shadow-adm-xs">
        <table class="w-full min-w-[720px] border-collapse text-left text-sm">
            <thead>
                <tr class="border-b border-hairline bg-surface-raised/40 text-[11px] font-medium uppercase tracking-wider text-muted">
                    <th class="px-4 py-3 font-semibold">Received</th>
                    <th class="px-4 py-3 font-semibold">Member</th>
                    <th class="px-4 py-3 font-semibold">Move</th>
                    <th class="px-4 py-3 font-semibold">Status</th>
                    <th class="px-4 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($requests as $req)
                    <tr class="border-b border-hairline last:border-b-0 hover:bg-surface-raised">
                        <td class="px-4 py-3 text-muted">{{ $req->created_at->format('j M Y') }}</td>
                        <td class="px-4 py-3 text-cream">{{ $req->user?->name }}<br><span class="text-xs text-body">{{ $req->user?->email }}</span></td>
                        <td class="px-4 py-3 text-body">{{ $req->fromPlan?->name ?? 'Membership' }} &rarr; {{ $req->toPlan->name }}</td>
                        <td class="px-4 py-3 text-body">{{ ucfirst($req->status) }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.plan-requests.show', $req) }}" class="text-xs font-semibold uppercase tracking-wider text-gold hover:text-gold-bright">Review</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-muted">No requests here.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin.shell>
