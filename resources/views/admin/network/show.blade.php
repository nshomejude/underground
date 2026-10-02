<x-admin.shell title="Connection" max-width="max-w-3xl">
    <div class="flex flex-col gap-6 rounded-adm border border-hairline bg-surface px-5 py-6 shadow-adm-xs sm:px-8 sm:py-8">
        <dl class="grid gap-4 text-sm sm:grid-cols-2">
            <div><dt class="text-[13px] text-body">Requester</dt><dd class="text-cream">{{ $connection->requester?->name }} ({{ $connection->requester?->email }})</dd></div>
            <div><dt class="text-[13px] text-body">Addressee</dt><dd class="text-cream">{{ $connection->addressee?->name }} ({{ $connection->addressee?->email }})</dd></div>
            <div><dt class="text-[13px] text-body">Kind</dt><dd class="text-cream">{{ $connection->isCollaboration() ? 'Collaboration' : 'Connection' }}</dd></div>
            <div><dt class="text-[13px] text-body">Status</dt><dd class="text-cream">{{ ucfirst($connection->status) }}</dd></div>
            <div><dt class="text-[13px] text-body">Sent</dt><dd class="text-cream">{{ $connection->created_at->format('j M Y, H:i') }}</dd></div>
            @if ($connection->responded_at)
                <div><dt class="text-[13px] text-body">Responded</dt><dd class="text-cream">{{ $connection->responded_at->format('j M Y, H:i') }}</dd></div>
            @endif
            @if ($connection->topic)
                <div><dt class="text-[13px] text-body">Topic</dt><dd class="text-cream">{{ $connection->topic }}</dd></div>
            @endif
            @if (! empty($connection->shared_sectors))
                <div><dt class="text-[13px] text-body">Shared sectors</dt><dd class="text-cream">{{ implode(', ', $connection->shared_sectors) }}</dd></div>
            @endif
            @if ($connection->isBlocked() && $connection->blocked_by)
                <div><dt class="text-[13px] text-body">Blocked by</dt><dd class="text-cream">{{ $connection->blocked_by === $connection->requester_id ? $connection->requester?->name : $connection->addressee?->name }}</dd></div>
            @endif
            <div><dt class="text-[13px] text-body">Conversation</dt><dd class="text-cream">{{ $connection->conversation ? 'Open (message content is private)' : 'None' }}</dd></div>
        </dl>

        <div>
            <p class="text-[13px] text-body">Request message</p>
            <p class="text-cream">{{ $connection->message ?: 'No message.' }}</p>
        </div>

        <a href="{{ route('admin.network.index') }}" class="text-xs font-semibold uppercase tracking-wider text-muted hover:text-cream">Back to network</a>
    </div>
</x-admin.shell>
