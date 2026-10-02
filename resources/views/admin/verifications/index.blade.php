<x-admin.shell title="Verifications">
    @php
        $tabs = ['pending' => 'Pending', 'submitted' => 'Submitted', 'in_review' => 'In review', 'approved' => 'Approved', 'rejected' => 'Rejected', 'all' => 'All'];
        $types = ['' => 'All types', 'identity' => 'Identity', 'company' => 'Company'];
        $badge = ['submitted' => 'av-badge--new', 'in_review' => 'av-badge--review', 'approved' => 'av-badge--ok', 'rejected' => 'av-badge--bad'];
    @endphp

    <nav class="mb-3 flex flex-wrap gap-2" aria-label="Filter by status">
        @foreach ($tabs as $s => $label)
            <a href="{{ route('admin.verifications.index', array_filter(['status' => $s, 'type' => $type, 'q' => $q, 'order' => $order])) }}"
               class="rounded-adm border px-3 py-1.5 text-xs font-semibold uppercase tracking-wider {{ $status === $s ? 'border-gold text-gold' : 'border-hairline text-body hover:text-cream' }}">{{ $label }}</a>
        @endforeach
    </nav>

    <form method="GET" class="av-filters mb-4" role="search">
        <input type="hidden" name="status" value="{{ $status }}">
        <label class="sr-only" for="av-q">Search applicants</label>
        <input id="av-q" type="search" name="q" value="{{ $q }}" placeholder="Name, email, company" class="av-input">
        <label class="sr-only" for="av-type">Type</label>
        <select id="av-type" name="type" class="av-input">
            @foreach ($types as $value => $label)
                <option value="{{ $value }}" @selected((string) $type === (string) $value)>{{ $label }}</option>
            @endforeach
        </select>
        <label class="sr-only" for="av-order">Order</label>
        <select id="av-order" name="order" class="av-input">
            <option value="oldest" @selected($order === 'oldest')>Oldest first</option>
            <option value="newest" @selected($order === 'newest')>Newest first</option>
        </select>
        <x-button variant="secondary" type="submit">Filter</x-button>
    </form>

    <div class="overflow-x-auto rounded-adm border border-hairline bg-surface shadow-adm-xs">
        <table class="w-full min-w-[720px] border-collapse text-left text-sm">
            <thead>
                <tr class="border-b border-hairline bg-surface-raised/40 text-[11px] font-medium uppercase tracking-wider text-muted">
                    <th class="px-4 py-3 font-semibold">Applicant</th>
                    <th class="px-4 py-3 font-semibold">Type</th>
                    <th class="px-4 py-3 font-semibold">Submitted</th>
                    <th class="px-4 py-3 font-semibold">Status</th>
                    <th class="px-4 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $item)
                    @php($row = $item['row'])
                    <tr class="border-b border-hairline last:border-b-0 hover:bg-surface-raised">
                        <td class="px-4 py-3 text-cream">
                            {{ $row->user?->name }}
                            <br><span class="text-xs text-body">{{ $item['kind'] === 'company' ? $row->company_name : $row->full_name_on_document }} &middot; {{ $row->user?->email }}</span>
                        </td>
                        <td class="px-4 py-3 text-body">{{ ucfirst($item['kind']) }}</td>
                        <td class="px-4 py-3 text-muted">{{ $item['at']?->format('j M Y, H:i') }}</td>
                        <td class="px-4 py-3">
                            <span class="av-badge {{ $badge[$row->status] ?? '' }}">{{ ucfirst(str_replace('_', ' ', $row->status)) }}</span>
                            @if ($row->info_request)<span class="av-badge av-badge--info">Info requested</span>@endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.verifications.show', [$item['kind'], $row->id]) }}" class="text-xs font-semibold uppercase tracking-wider text-gold hover:text-gold-bright">Review</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-muted">No verifications here.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin.shell>
