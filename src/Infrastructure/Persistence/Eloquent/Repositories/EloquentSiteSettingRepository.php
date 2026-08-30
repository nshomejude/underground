<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Repositories;

use Domain\Content\Entities\SiteSetting;
use Domain\Content\Repositories\SiteSettingRepository;
use Illuminate\Database\QueryException;
use Infrastructure\Persistence\Eloquent\Models\SiteSettingRecord;

final class EloquentSiteSettingRepository implements SiteSettingRepository
{
    public function current(): SiteSetting
    {
        // x-layout and x-site-footer read this on every single page load,
        // public and admin alike, so a missing table (a fresh install
        // before migrations run, or a test hitting a page without
        // RefreshDatabase) must degrade to defaults rather than 500 the
        // whole site.
        try {
            $record = SiteSettingRecord::query()->latest('id')->first();
        } catch (QueryException) {
            $record = null;
        }

        // No row yet (fresh install before the seeder/first save runs) —
        // hand back sane defaults rather than forcing every caller to
        // handle a missing-settings case.
        if ($record === null) {
            return new SiteSetting(
                siteName: config('app.name', 'Underground Network'),
                siteTagline: 'Power Beneath The Surface',
                contactEmail: 'office@underground-network.example',
                contactPhone: null,
                socialLinks: [],
                footerNote: null,
                metaTitle: config('app.name', 'Underground Network'),
                metaDescription: 'A global network delivering discreet, high-conviction execution across sectors and borders.',
                ogImageUrl: null,
                twitterHandle: null,
                maintenanceMode: false,
                maintenanceMessage: null,
                publicRegistrationEnabled: true,
            );
        }

        return $this->toEntity($record);
    }

    /**
     * Overwrites the single settings row. There is exactly one row: if it
     * already exists, its attributes are replaced in place (keeping its
     * id); otherwise the first row is created.
     */
    public function update(SiteSetting $setting): void
    {
        $record = SiteSettingRecord::query()->latest('id')->first() ?? new SiteSettingRecord;

        $record->fill([
            'site_name' => $setting->siteName,
            'site_tagline' => $setting->siteTagline,
            'contact_email' => $setting->contactEmail,
            'contact_phone' => $setting->contactPhone,
            'social_links' => $setting->socialLinks,
            'footer_note' => $setting->footerNote,
            'meta_title' => $setting->metaTitle,
            'meta_description' => $setting->metaDescription,
            'og_image_url' => $setting->ogImageUrl,
            'twitter_handle' => $setting->twitterHandle,
            'maintenance_mode' => $setting->maintenanceMode,
            'maintenance_message' => $setting->maintenanceMessage,
            'public_registration_enabled' => $setting->publicRegistrationEnabled,
        ])->save();
    }

    private function toEntity(SiteSettingRecord $record): SiteSetting
    {
        return new SiteSetting(
            siteName: $record->site_name,
            siteTagline: $record->site_tagline,
            contactEmail: $record->contact_email,
            contactPhone: $record->contact_phone,
            socialLinks: $record->social_links ?? [],
            footerNote: $record->footer_note,
            metaTitle: $record->meta_title,
            metaDescription: $record->meta_description,
            ogImageUrl: $record->og_image_url,
            twitterHandle: $record->twitter_handle,
            maintenanceMode: (bool) $record->maintenance_mode,
            maintenanceMessage: $record->maintenance_message,
            publicRegistrationEnabled: (bool) $record->public_registration_enabled,
        );
    }
}
