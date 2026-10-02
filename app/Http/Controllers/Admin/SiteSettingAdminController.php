<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SiteSettingRequest;
use Domain\Content\Entities\SiteSetting;
use Domain\Content\Repositories\SiteSettingRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * A single-record settings-style admin screen for the SiteSetting
 * singleton: general contact info, social/footer links, SEO defaults, and
 * maintenance/feature toggles. No index/create/delete — see
 * Domain\Content\Entities\SiteSetting and SiteSettingRepository.
 */
final class SiteSettingAdminController extends Controller
{
    public function __construct(private readonly SiteSettingRepository $settings) {}

    public function edit(): View
    {
        return view('admin.settings.edit', ['setting' => $this->settings->current()]);
    }

    public function update(SiteSettingRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $socialLinks = [];
        foreach ($data['social_links'] ?? [] as $row) {
            $label = trim((string) ($row['label'] ?? ''));
            $url = trim((string) ($row['url'] ?? ''));

            if ($label !== '' && $url !== '') {
                $socialLinks[] = ['label' => $label, 'url' => $url];
            }
        }

        $this->settings->update(new SiteSetting(
            siteName: $data['site_name'],
            siteTagline: $data['site_tagline'],
            contactEmail: $data['contact_email'],
            contactPhone: $data['contact_phone'] ?: null,
            socialLinks: $socialLinks,
            footerNote: $data['footer_note'] ?: null,
            metaTitle: $data['meta_title'],
            metaDescription: $data['meta_description'],
            ogImageUrl: $data['og_image_url'] ?: null,
            twitterHandle: $data['twitter_handle'] ?: null,
            maintenanceMode: (bool) ($data['maintenance_mode'] ?? false),
            maintenanceMessage: $data['maintenance_message'] ?: null,
            publicRegistrationEnabled: (bool) ($data['public_registration_enabled'] ?? false),
            gearAnimationEnabled: (bool) ($data['gear_animation_enabled'] ?? false),
            networkAnimationEnabled: (bool) ($data['network_animation_enabled'] ?? false),
        ));

        return redirect()->route('admin.settings.edit')->with('status', 'Site configuration updated.');
    }
}
