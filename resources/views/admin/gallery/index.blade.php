<x-admin.shell title="Gallery">
    <x-slot:actions>
        <x-button variant="primary" href="{{ route('admin.gallery.create') }}">
            <x-icon name="chevron-right" class="h-3.5 w-3.5 rotate-[-45deg]" />
            New Photo
        </x-button>
    </x-slot:actions>

    <div class="overflow-x-auto rounded-adm border border-hairline bg-surface shadow-adm-xs">
        <table class="w-full min-w-[720px] border-collapse text-left text-sm">
            <thead>
                <tr class="border-b border-hairline bg-surface-raised/40 text-[11px] font-medium uppercase tracking-wider text-muted">
                    <th class="px-4 py-3 font-semibold">Position</th>
                    <th class="px-4 py-3 font-semibold">Caption</th>
                    <th class="px-4 py-3 font-semibold">Date</th>
                    <th class="px-4 py-3 font-semibold">Image</th>
                    <th class="px-4 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($photos as $photo)
                    <tr class="border-b border-hairline transition-colors last:border-b-0 hover:bg-surface-raised">
                        <td class="px-4 py-3 text-muted">{{ $photo->position }}</td>
                        <td class="px-4 py-3 text-cream">{{ $photo->caption }}</td>
                        <td class="px-4 py-3 text-body">{{ $photo->capturedOn ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if ($photo->imageUrl)
                                <span class="inline-flex items-center gap-1.5 text-success">
                                    <x-icon name="check-circle" class="h-4 w-4" />
                                    Set
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-muted">
                                    <x-icon name="camera" class="h-4 w-4" />
                                    Pending
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-4">
                                <a href="{{ route('admin.gallery.edit', $photo->slug->value) }}" class="text-xs font-semibold uppercase tracking-wider text-gold hover:text-gold-bright">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.gallery.destroy', $photo->slug->value) }}" onsubmit="return confirm('Remove this photo?');">
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
                        <td colspan="5" class="px-4 py-6 text-center text-muted">No photos yet — /team/gallery shows its empty state until one is added here.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin.shell>
