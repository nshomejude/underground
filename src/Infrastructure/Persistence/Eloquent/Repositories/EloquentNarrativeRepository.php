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
