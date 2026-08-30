<x-layout title="Home">
    <section class="mx-auto flex max-w-7xl flex-col items-start gap-8 px-4 py-20 sm:px-6 lg:px-8 lg:py-32">
        <x-section-heading eyebrow="Strategic Influence. Real Outcomes.">
            Power Beneath<br class="hidden sm:block"> the Surface
        </x-section-heading>

        <p class="max-w-xl text-base leading-relaxed text-body">
            Underground Network Inc. is a discreet global strategic-advisory and influence firm. This
            page is a placeholder that proves the shared design system &mdash; header, footer,
            navigation, and reusable components &mdash; is wired correctly. The full landing page
            ships in a later module.
        </p>

        <div class="flex flex-wrap items-center gap-4">
            <x-button variant="primary" href="#">
                Our Capabilities
                <x-icon name="chevron-right" class="h-3.5 w-3.5" />
            </x-button>
            <x-button variant="secondary" href="#">
                Who We Are
                <x-icon name="chevron-right" class="h-3.5 w-3.5" />
            </x-button>
        </div>

        <div class="flex flex-wrap gap-3 pt-4">
            <x-status-badge label="Operational" tone="success" />
            <x-status-badge label="Pending Review" tone="warning" />
            <x-status-badge label="Access Restricted" tone="danger" />
            <x-status-badge label="Briefing Scheduled" tone="info" />
            <x-status-badge label="Archived" tone="neutral" />
        </div>
    </section>
</x-layout>
