@php
    $navigationRows = 8;
    $existingNav = old('navigation', $narrative->navigation);
    $navRows = array_pad(array_slice($existingNav, 0, $navigationRows), $navigationRows, ['label' => '', 'href' => '']);
@endphp

<x-admin.shell title="Narrative">
    <p class="max-w-2xl text-sm leading-relaxed text-body">
        This is the firm's authored brand copy &mdash; the fixed positioning language behind the landing page.
        There is exactly one narrative; saving here replaces it in place.
    </p>

    <form method="POST" action="{{ route('admin.narrative.update') }}" class="flex flex-col gap-10">
        @csrf
        @method('PUT')

        <fieldset class="flex flex-col gap-6 border border-border bg-surface px-6 py-8 sm:px-10 sm:py-10">
            <legend class="px-2 font-serif text-lg text-cream">Identity</legend>
            <x-admin.field name="company" label="Company" :value="$narrative->company" />
            <x-admin.field name="tagline" label="Tagline" :value="$narrative->tagline" />
            <x-admin.field name="copyright" label="Copyright Line" :value="$narrative->copyright" />
        </fieldset>

        <fieldset class="flex flex-col gap-6 border border-border bg-surface px-6 py-8 sm:px-10 sm:py-10">
            <legend class="px-2 font-serif text-lg text-cream">Hero</legend>
            <x-admin.field name="eyebrow" label="Eyebrow" :value="$narrative->eyebrow" />
            <x-admin.textarea-field name="headline_text" label="Headline (one line per rendered line)" :value="old('headline_text', implode(PHP_EOL, $narrative->headline))" :rows="3" />
            <x-admin.field name="accent_line" label="Accent Line" :value="$narrative->accentLine" />
            <x-admin.textarea-field name="intro" label="Intro" :value="$narrative->intro" />

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <x-admin.field name="primary_cta_label" label="Primary CTA Label" :value="$narrative->primaryCta['label']" />
                <x-admin.field name="primary_cta_href" label="Primary CTA Href" :value="$narrative->primaryCta['href']" />
                <x-admin.field name="secondary_cta_label" label="Secondary CTA Label" :value="$narrative->secondaryCta['label']" />
                <x-admin.field name="secondary_cta_href" label="Secondary CTA Href" :value="$narrative->secondaryCta['href']" />
            </div>
        </fieldset>

        <fieldset class="flex flex-col gap-6 border border-border bg-surface px-6 py-8 sm:px-10 sm:py-10">
            <legend class="px-2 font-serif text-lg text-cream">Creed</legend>
            <x-admin.field name="creed_title" label="Creed Title" :value="$narrative->creedTitle" />
            <x-admin.textarea-field name="creed_body" label="Creed Body" :value="$narrative->creedBody" />
        </fieldset>

        <fieldset class="flex flex-col gap-6 border border-border bg-surface px-6 py-8 sm:px-10 sm:py-10">
            <legend class="px-2 font-serif text-lg text-cream">Section Headings</legend>
            <x-admin.field name="capabilities_eyebrow" label="Capabilities Eyebrow" :value="$narrative->capabilitiesEyebrow" />
            <x-admin.field name="capabilities_heading" label="Capabilities Heading" :value="$narrative->capabilitiesHeading" />
            <x-admin.field name="sectors_heading" label="Sectors Heading" :value="$narrative->sectorsHeading" />
            <x-admin.field name="engagement_heading" label="Engagement Heading" :value="$narrative->engagementHeading" />
        </fieldset>

        <fieldset class="flex flex-col gap-6 border border-border bg-surface px-6 py-8 sm:px-10 sm:py-10">
            <legend class="px-2 font-serif text-lg text-cream">Reach</legend>
            <x-admin.field name="reach_heading" label="Reach Heading" :value="$narrative->reachHeading" />
            <x-admin.textarea-field name="reach_body" label="Reach Body" :value="$narrative->reachBody" />
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <x-admin.field name="reach_cta_label" label="Reach CTA Label" :value="$narrative->reachCta['label']" />
                <x-admin.field name="reach_cta_href" label="Reach CTA Href" :value="$narrative->reachCta['href']" />
            </div>
        </fieldset>

        <fieldset class="flex flex-col gap-6 border border-border bg-surface px-6 py-8 sm:px-10 sm:py-10">
            <legend class="px-2 font-serif text-lg text-cream">Closing</legend>
            <x-admin.field name="closing_heading" label="Closing Heading" :value="$narrative->closingHeading" />
            <x-admin.textarea-field name="closing_support" label="Closing Support" :value="$narrative->closingSupport" />
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <x-admin.field name="closing_cta_label" label="Closing CTA Label" :value="$narrative->closingCta['label']" />
                <x-admin.field name="closing_cta_href" label="Closing CTA Href" :value="$narrative->closingCta['href']" />
            </div>
        </fieldset>

        <fieldset class="flex flex-col gap-4 border border-border bg-surface px-6 py-8 sm:px-10 sm:py-10">
            <legend class="px-2 font-serif text-lg text-cream">Navigation</legend>
            <p class="text-xs text-muted">Leave a row's label and href both blank to drop it.</p>

            @foreach ($navRows as $index => $row)
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <x-admin.field
                        :name="'navigation['.$index.'][label]'"
                        :error-key="'navigation.'.$index.'.label'"
                        :label="'Row '.($index + 1).' Label'"
                        :value="$row['label'] ?? ''"
                        :required="false"
                    />
                    <x-admin.field
                        :name="'navigation['.$index.'][href]'"
                        :error-key="'navigation.'.$index.'.href'"
                        :label="'Row '.($index + 1).' Href'"
                        :value="$row['href'] ?? ''"
                        :required="false"
                    />
                </div>
            @endforeach
        </fieldset>

        <div class="flex items-center gap-4">
            <x-button variant="primary" type="submit">Save Narrative</x-button>
        </div>
    </form>
</x-admin.shell>
