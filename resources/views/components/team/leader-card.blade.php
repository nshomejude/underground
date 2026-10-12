@props([
    'leader',
    'founderPortraitSrc',
])

{{-- Shared with resources/views/team/index.blade.php's card markup — kept
     as its own component so the Senior Leadership tabs (which repeat this
     three times) and the main roster render identical cards. --}}
<div class="flex flex-col items-start gap-5 border border-border bg-surface p-8">
    @if (!empty($leader['portrait']))
        <div class="h-20 w-20 overflow-hidden rounded-full border border-gold">
            <img
                src="{{ $founderPortraitSrc }}"
                alt="Portrait of {{ $leader['name'] }}"
                class="h-full w-full object-cover"
                loading="lazy"
            >
        </div>
    @else
        <span class="flex h-20 w-20 items-center justify-center rounded-full border border-gold bg-surface">
            <x-icon name="{{ $leader['icon'] ?? 'user' }}" class="h-8 w-8 text-gold" />
        </span>
    @endif

    <div class="flex flex-col gap-2">
        <h3 class="font-serif text-xl font-semibold leading-snug text-cream">
            {{ $leader['name'] }}
        </h3>
        <p class="text-xs font-semibold uppercase tracking-widest text-gold">
            {{ $leader['title'] }}
        </p>
        <p class="text-sm leading-relaxed text-body">{{ $leader['background'] }}</p>

        <div class="mt-2 flex flex-wrap items-center gap-3 border-t border-border pt-3 text-[11px] font-semibold uppercase tracking-wide text-muted">
            <span class="inline-flex items-center gap-1.5">
                <x-icon name="briefcase" class="h-3.5 w-3.5 text-gold" />
                {{ $leader['employment_status_label'] }}
            </span>
            @if (!empty($leader['languages']))
                <span class="inline-flex items-center gap-1.5">
                    <x-icon name="globe" class="h-3.5 w-3.5 text-gold" />
                    {{ implode(', ', $leader['languages']) }}
                </span>
            @endif
        </div>
    </div>
</div>
