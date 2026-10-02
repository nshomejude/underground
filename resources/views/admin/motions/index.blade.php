<x-admin.shell title="Motions">
    <div class="vt">
        @if (session('status'))
            <div class="vt-alert" role="status"><x-icon name="check-circle" /><span>{{ session('status') }}</span></div>
        @endif

        <form method="GET" action="{{ route('admin.motions.index') }}" class="vt-filters">
            <div class="vt-field">
                <label class="vt-label" for="f-status">Status</label>
                <select class="vt-select" id="f-status" name="status">
                    <option value="">All</option>
                    @foreach ($statuses as $s)
                        <option value="{{ $s }}" @selected(($filters['status'] ?? '') === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="vt-field">
                <label class="vt-label" for="f-kind">Kind</label>
                <select class="vt-select" id="f-kind" name="kind">
                    <option value="">Any</option>
                    @foreach (['decision', 'election', 'poll'] as $k)
                        <option value="{{ $k }}" @selected(($filters['kind'] ?? '') === $k)>{{ ucfirst($k) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="vt-field">
                <label class="vt-label" for="f-q">Title</label>
                <input class="vt-input" id="f-q" name="q" type="search" value="{{ $filters['q'] ?? '' }}">
            </div>
            <button type="submit" class="vt-btn">Filter</button>
            <a class="vt-btn" href="{{ route('admin.motions.export', $filters) }}"><x-icon name="download" />Export CSV</a>
            <a class="vt-btn vt-btn-solid" href="{{ route('admin.motions.create') }}"><x-icon name="plus" />New motion</a>
        </form>

        <p class="vt-hint" style="margin-bottom: 12px">{{ $motions->total() }} {{ \Illuminate\Support\Str::plural('motion', $motions->total()) }}</p>

        <div class="vt-tablewrap">
            <table class="vt-table">
                <thead>
                    <tr><th>Motion</th><th>Kind</th><th>Status</th><th>Voters</th><th>Result</th><th>Closes</th><th><span class="vt-sr">Actions</span></th></tr>
                </thead>
                <tbody>
                    @forelse ($motions as $m)
                        <tr>
                            <td>{{ $m->title }}<br><span class="vt-hint">{{ $m->tierLabel() }}{{ $m->creator ? ' · by '.$m->creator->name : '' }}</span></td>
                            <td>{{ $m->kindLabel() }}</td>
                            <td>{{ ucfirst($m->status) }}</td>
                            <td>{{ $m->votes_count }} / {{ $m->quorum }}</td>
                            <td>
                                @if ($m->isDecided() && is_array($m->result))
                                    {{ ucfirst(str_replace('_', ' ', (string) ($m->outcome ?? $m->result['outcome'] ?? ''))) }}
                                    @foreach (($m->result['counts'] ?? []) as $c => $n)<br><span class="vt-hint">{{ $c }}: {{ $n }}</span>@endforeach
                                @else
                                    <span class="vt-hint">Not decided</span>
                                @endif
                            </td>
                            <td>{{ $m->closes_at?->format('j M Y, H:i') }}</td>
                            <td><a href="{{ route('admin.motions.show', $m) }}">Open</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7">No motions match.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 16px">{{ $motions->links() }}</div>
    </div>
</x-admin.shell>
