<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Infrastructure\Persistence\Eloquent\Models\SiteSettingRecord;

/**
 * Seeds the single site-settings row with the values previously hardcoded
 * across ContactController, the footer, and page <head> tags.
 */
final class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        if (SiteSettingRecord::query()->exists()) {
            return;
        }

        SiteSettingRecord::query()->create([
            'site_name' => 'Underground Network',
            'site_tagline' => 'Power Beneath The Surface',
            'contact_email' => 'under@un-der.com',
            'contact_phone' => '+1-571-508-9170',
            'social_links' => [],
            'footer_note' => null,
            'meta_title' => 'Underground Network',
            'meta_description' => 'A global network delivering discreet, high-conviction execution across sectors and borders.',
            'og_image_url' => null,
            'twitter_handle' => null,
            'maintenance_mode' => false,
            'maintenance_message' => null,
            'public_registration_enabled' => true,
        ]);
    }
}
