<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NarrativeRequest;
use Domain\Content\Entities\Narrative;
use Domain\Content\Repositories\NarrativeRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * A single-record settings-style admin screen for the Narrative singleton
 * (the firm's authored brand copy). No index/create/delete — see
 * Domain\Content\Entities\Narrative and NarrativeRepository.
 */
final class NarrativeAdminController extends Controller
{
    public function __construct(private readonly NarrativeRepository $narratives) {}

    public function edit(): View
    {
        return view('admin.narrative.edit', ['narrative' => $this->narratives->current()]);
    }

    public function update(NarrativeRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $headline = array_values(array_filter(
            array_map('trim', explode("\n", str_replace("\r\n", "\n", $data['headline_text']))),
            fn (string $line): bool => $line !== '',
        ));

        $navigation = [];
        foreach ($data['navigation'] ?? [] as $row) {
            $label = trim((string) ($row['label'] ?? ''));
            $href = trim((string) ($row['href'] ?? ''));

            if ($label !== '' && $href !== '') {
                $navigation[] = ['label' => $label, 'href' => $href];
            }
        }

        $narrative = new Narrative(
            company: $data['company'],
            tagline: $data['tagline'],
            eyebrow: $data['eyebrow'],
            headline: $headline,
            accentLine: $data['accent_line'],
            intro: $data['intro'],
            primaryCta: ['label' => $data['primary_cta_label'], 'href' => $data['primary_cta_href']],
            secondaryCta: ['label' => $data['secondary_cta_label'], 'href' => $data['secondary_cta_href']],
            creedTitle: $data['creed_title'],
            creedBody: $data['creed_body'],
            capabilitiesEyebrow: $data['capabilities_eyebrow'],
            capabilitiesHeading: $data['capabilities_heading'],
            sectorsHeading: $data['sectors_heading'],
            reachHeading: $data['reach_heading'],
            reachBody: $data['reach_body'],
            reachCta: ['label' => $data['reach_cta_label'], 'href' => $data['reach_cta_href']],
            engagementHeading: $data['engagement_heading'],
            closingHeading: $data['closing_heading'],
            closingSupport: $data['closing_support'],
            closingCta: ['label' => $data['closing_cta_label'], 'href' => $data['closing_cta_href']],
            navigation: $navigation,
            copyright: $data['copyright'],
        );

        $this->narratives->update($narrative);

        return redirect()->route('admin.narrative.edit')->with('status', 'Narrative updated.');
    }
}
