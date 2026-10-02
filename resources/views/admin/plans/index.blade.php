<x-admin.shell title="Membership Plans">
    <x-slot:actions>
        <x-button variant="primary" href="{{ route('admin.plans.create') }}">New Plan</x-button>
    </x-slot:actions>

    @error('plan')
        <p class="mb-4 text-sm text-danger" role="alert">{{ $message }}</p>
    @enderror

    <div class="overflow-x-auto rounded-adm border border-hairline bg-surface shadow-adm-xs">
        <table class="w-full min-w-[720px] border-collapse text-left text-sm">
            <thead>
                <tr class="border-b border-hairline bg-surface-raised/40 text-[11px] font-medium uppercase tracking-wider text-muted">
                    <th class="px-4 py-3 font-semibold">Position</th>
                    <th class="px-4 py-3 font-semibold">Plan</th>
                    <th class="px-4 py-3 font-semibold">Tier</th>
                    <th class="px-4 py-3 font-semibold">Price</th>
                    <th class="px-4 py-3 font-semibold">Status</th>
                    <th class="px-4 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($plans as $plan)
                    <tr class="border-b border-hairline transition-colors last:border-b-0 hover:bg-surface-raised">
                        <td class="px-4 py-3 text-muted">{{ $plan->position }}</td>
                        <td class="px-4 py-3 text-cream">{{ $plan->name }}</td>
                        <td class="px-4 py-3 text-body">{{ $plan->tier_slug }}</td>
                        <td class="px-4 py-3 text-body">{{ $plan->priceLabel() }} {{ $plan->intervalLabel() }}</td>
                        <td class="px-4 py-3 text-body">{{ $plan->is_active ? 'Active' : 'Hidden' }}{{ $plan->is_featured ? ' · Featured' : '' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-4">
                                <a href="{{ route('admin.plans.edit', $plan) }}" class="text-xs font-semibold uppercase tracking-wider text-gold hover:text-gold-bright">Edit</a>
                                <form method="POST" action="{{ route('admin.plans.destroy', $plan) }}" onsubmit="return confirm('Delete this plan?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold uppercase tracking-wider text-danger hover:opacity-80">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-muted">No plans yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin.shell>
