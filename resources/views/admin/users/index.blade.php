<x-admin.shell title="Users" eyebrow="Staff" description="Every registered account, and who currently has staff admin access.">
    <div class="overflow-x-auto rounded-adm border border-hairline bg-surface shadow-adm-xs">
        <table class="w-full min-w-[640px] border-collapse text-left text-sm">
            <thead>
                <tr class="border-b border-hairline bg-surface-raised/40 text-[11px] font-medium uppercase tracking-wider text-muted">
                    <th class="px-4 py-3 font-semibold">Name</th>
                    <th class="px-4 py-3 font-semibold">Email</th>
                    <th class="px-4 py-3 font-semibold">Joined</th>
                    <th class="px-4 py-3 font-semibold">Verified</th>
                    <th class="px-4 py-3 font-semibold">Role</th>
                    <th class="px-4 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr class="border-b border-hairline transition-colors last:border-b-0 hover:bg-surface-raised">
                        <td class="px-4 py-3 text-cream">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-body">{{ $user->email }}</td>
                        <td class="px-4 py-3 text-muted">{{ $user->created_at->format('j M Y') }}</td>
                        <td class="px-4 py-3">
                            @if ($user->email_verified_at)
                                <x-status-badge label="Verified" tone="success" />
                            @else
                                <x-status-badge label="Unverified" tone="neutral" />
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if ($user->is_admin)
                                <x-status-badge label="Staff Admin" tone="info" />
                            @else
                                <x-status-badge label="Member" tone="neutral" />
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-4">
                                @if ($user->is(auth()->user()))
                                    <span class="text-xs text-muted">You</span>
                                @else
                                    <form method="POST" action="{{ route('admin.users.toggle-admin', $user) }}" onsubmit="return confirm('{{ $user->is_admin ? 'Revoke' : 'Grant' }} admin access for {{ $user->name }}?');">
                                        @csrf
                                        <button type="submit" class="text-xs font-semibold uppercase tracking-wider {{ $user->is_admin ? 'text-danger hover:opacity-80' : 'text-gold hover:text-gold-bright' }}">
                                            {{ $user->is_admin ? 'Revoke Admin' : 'Make Admin' }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-muted">No users yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin.shell>
