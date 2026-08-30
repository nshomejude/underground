<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Repositories;

use Domain\Content\Entities\Narrative;
use Domain\Content\Repositories\NarrativeRepository;
use Domain\Shared\Exceptions\DomainException;
use Infrastructure\Persistence\Eloquent\Models\NarrativeRecord;

final class EloquentNarrativeRepository implements NarrativeRepository
{
    public function current(): Narrative
    {
        $record = NarrativeRecord::query()->latest('id')->first();

        if ($record === null) {
            throw new DomainException('No narrative has been authored yet.');
        }

        return $this->toEntity($record);
    }

    /**
     * Overwrites the single narrative row. There is exactly one authored
     * copy: if a row already exists, its attributes are replaced in place
     * (keeping its id); otherwise the first row is created.
     */
    public function update(Narrative $narrative): void
    {
        $record = NarrativeRecord::query()->latest('id')->first() ?? new NarrativeRecord;

        $record->fill([
            'company' => $narrative->company,
            'tagline' => $narrative->tagline,
            'eyebrow' => $narrative->eyebrow,
            'headline' => $narrative->headline,
            'accent_line' => $narrative->accentLine,
            'intro' => $narrative->intro,
            'primary_cta' => $narrative->primaryCta,
            'secondary_cta' => $narrative->secondaryCta,
            'creed_title' => $narrative->creedTitle,
            'creed_body' => $narrative->creedBody,
            'capabilities_eyebrow' => $narrative->capabilitiesEyebrow,
            'capabilities_heading' => $narrative->capabilitiesHeading,
            'sectors_heading' => $narrative->sectorsHeading,
            'reach_heading' => $narrative->reachHeading,
            'reach_body' => $narrative->reachBody,
            'reach_cta' => $narrative->reachCta,
            'engagement_heading' => $narrative->engagementHeading,
            'closing_heading' => $narrative->closingHeading,
            'closing_support' => $narrative->closingSupport,
            'closing_cta' => $narrative->closingCta,
            'navigation' => $narrative->navigation,
            'copyright' => $narrative->copyright,
        ])->save();
    }

    private function toEntity(NarrativeRecord $record): Narrative
    {
        return new Narrative(
            company: $record->company,
            tagline: $record->tagline,
            eyebrow: $record->eyebrow,
            headline: $record->headline,
            accentLine: $record->accent_line,
            intro: $record->intro,
            primaryCta: $record->primary_cta,
            secondaryCta: $record->secondary_cta,
            creedTitle: $record->creed_title,
            creedBody: $record->creed_body,
            capabilitiesEyebrow: $record->capabilities_eyebrow,
            capabilitiesHeading: $record->capabilities_heading,
            sectorsHeading: $record->sectors_heading,
            reachHeading: $record->reach_heading,
            reachBody: $record->reach_body,
            reachCta: $record->reach_cta,
            engagementHeading: $record->engagement_heading,
            closingHeading: $record->closing_heading,
            closingSupport: $record->closing_support,
            closingCta: $record->closing_cta,
            navigation: $record->navigation,
            copyright: $record->copyright,
        );
    }
}
