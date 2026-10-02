<?php

declare(strict_types=1);

namespace Domain\Content\Entities;

/**
 * Site-wide configuration: general contact info, social/footer links, SEO
 * defaults, and maintenance/feature toggles. A singleton — there is exactly
 * one row — same shape as Narrative but for operational settings rather
 * than authored landing-page copy.
 */
final readonly class SiteSetting
{
    /** @param  list<array{label:string,url:string}>  $socialLinks */
    public function __construct(
        public string $siteName,
        public string $siteTagline,
        public string $contactEmail,
        public ?string $contactPhone,
        public array $socialLinks,
        public ?string $footerNote,
        public string $metaTitle,
        public string $metaDescription,
        public ?string $ogImageUrl,
        public ?string $twitterHandle,
        public bool $maintenanceMode,
        public ?string $maintenanceMessage,
        public bool $publicRegistrationEnabled,
        public bool $gearAnimationEnabled = true,
        public bool $networkAnimationEnabled = true,
        public bool $blocksAnimationEnabled = true,
        public bool $globeAtlasEnabled = true,
    ) {}

    public function toArray(): array
    {
        return [
            'site_name' => $this->siteName,
            'site_tagline' => $this->siteTagline,
            'contact_email' => $this->contactEmail,
            'contact_phone' => $this->contactPhone,
            'social_links' => $this->socialLinks,
            'footer_note' => $this->footerNote,
            'meta_title' => $this->metaTitle,
            'meta_description' => $this->metaDescription,
            'og_image_url' => $this->ogImageUrl,
            'twitter_handle' => $this->twitterHandle,
            'maintenance_mode' => $this->maintenanceMode,
            'maintenance_message' => $this->maintenanceMessage,
            'public_registration_enabled' => $this->publicRegistrationEnabled,
            'gear_animation_enabled' => $this->gearAnimationEnabled,
            'network_animation_enabled' => $this->networkAnimationEnabled,
            'blocks_animation_enabled' => $this->blocksAnimationEnabled,
            'globe_atlas_enabled' => $this->globeAtlasEnabled,
        ];
    }
}
