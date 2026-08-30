@php
    $socialRows = 5;
    $existingSocial = old('social_links', $setting->socialLinks);
    $socialLinkRows = array_pad(array_slice($existingSocial, 0, $socialRows), $socialRows, ['label' => '', 'url' => '']);
@endphp

<x-admin.shell title="Site Configuration" eyebrow="Settings" max-width="max-w-3xl">
    <form method="POST" action="{{ route('admin.settings.update') }}" class="flex flex-col gap-8">
        @csrf
        @method('PUT')

        <fieldset class="flex flex-col gap-4 rounded-adm border border-hairline bg-surface px-5 py-6 shadow-adm-xs sm:px-8 sm:py-8">
            <legend class="px-1 text-sm font-semibold text-cream">General</legend>

            <x-admin.field name="site_name" label="Site Name" :value="$setting->siteName" />
            <x-admin.field name="site_tagline" label="Tagline" :value="$setting->siteTagline" />
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <x-admin.field name="contact_email" label="Contact Email" type="email" :value="$setting->contactEmail" />
                <x-admin.field name="contact_phone" label="Contact Phone" :value="$setting->contactPhone" :required="false" />
            </div>
        </fieldset>

        <fieldset class="flex flex-col gap-4 rounded-adm border border-hairline bg-surface px-5 py-6 shadow-adm-xs sm:px-8 sm:py-8">
            <legend class="px-1 text-sm font-semibold text-cream">Social &amp; Footer Links</legend>
            <p class="text-xs text-muted">Leave a row's label and URL both blank to drop it. Shown in the site footer when at least one link is set.</p>

            @foreach ($socialLinkRows as $index => $row)
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <x-admin.field
                        :name="'social_links['.$index.'][label]'"
                        :error-key="'social_links.'.$index.'.label'"
                        :label="'Row '.($index + 1).' Label'"
                        placeholder="LinkedIn"
                        :value="$row['label'] ?? ''"
                        :required="false"
                    />
                    <x-admin.field
                        :name="'social_links['.$index.'][url]'"
                        :error-key="'social_links.'.$index.'.url'"
                        :label="'Row '.($index + 1).' URL'"
                        placeholder="https://linkedin.com/company/..."
                        :value="$row['url'] ?? ''"
                        :required="false"
                    />
                </div>
            @endforeach

            <x-admin.field name="footer_note" label="Footer Note" :value="$setting->footerNote" :required="false" />
        </fieldset>

        <fieldset class="flex flex-col gap-4 rounded-adm border border-hairline bg-surface px-5 py-6 shadow-adm-xs sm:px-8 sm:py-8">
            <legend class="px-1 text-sm font-semibold text-cream">SEO &amp; Meta Cards</legend>
            <p class="text-xs text-muted">Used for the page &lt;title&gt;, search results, and the preview card shown when a page is shared on social media.</p>

            <x-admin.field name="meta_title" label="Default Meta Title" :value="$setting->metaTitle" />
            <x-admin.textarea-field name="meta_description" label="Default Meta Description" rows="3" :value="$setting->metaDescription" />
            <x-admin.field name="og_image_url" label="Social Share Image URL" :value="$setting->ogImageUrl" :required="false" placeholder="https://.../share-card.jpg" />
            <x-admin.field name="twitter_handle" label="X / Twitter Handle" :value="$setting->twitterHandle" :required="false" placeholder="@underground" />
        </fieldset>

        <fieldset class="flex flex-col gap-4 rounded-adm border border-hairline bg-surface px-5 py-6 shadow-adm-xs sm:px-8 sm:py-8">
            <legend class="px-1 text-sm font-semibold text-cream">Maintenance &amp; Feature Toggles</legend>

            <x-admin.checkbox-field name="maintenance_mode" label="Maintenance mode — takes the public site offline for everyone but staff" :checked="$setting->maintenanceMode" />
            <x-admin.textarea-field name="maintenance_message" label="Maintenance Message" rows="2" :value="$setting->maintenanceMessage" :required="false" />

            <div class="border-t border-hairline pt-4">
                <x-admin.checkbox-field name="public_registration_enabled" label="Allow new member account registration" :checked="$setting->publicRegistrationEnabled" />
            </div>
        </fieldset>

        <div class="flex items-center gap-4">
            <x-button variant="primary" type="submit">Save Configuration</x-button>
        </div>
    </form>
</x-admin.shell>
