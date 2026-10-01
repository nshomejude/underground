@props([
    'heading' => 'Start a confidential conversation.',
    'text' => 'A partner reviews every inquiry personally. Nothing you share leaves the firm.',
    'primaryLabel' => 'Start a Confidential Inquiry',
    'primaryHref' => null,
    'tone' => 'surface',
])

<section class="border-t border-border {{ $tone === 'ink' ? 'bg-ink' : 'bg-surface' }}">
    <div class="mx-auto flex max-w-7xl flex-col items-start gap-6 px-4 py-16 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8 lg:py-20">
        <div class="flex flex-col gap-2">
            <h2 class="font-serif text-2xl font-semibold text-cream sm:text-3xl">{{ $heading }}</h2>
            <p class="max-w-xl text-sm leading-relaxed text-body">{{ $text }}</p>
        </div>
        <div class="flex flex-wrap gap-4">
            <x-button variant="primary" href="{{ $primaryHref ?? route('inquiries.create') }}">
                {{ $primaryLabel }}
                <x-icon name="lock" class="h-3.5 w-3.5" />
            </x-button>
            {{ $slot }}
        </div>
    </div>
</section>
