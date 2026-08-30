<?php

declare(strict_types=1);

namespace Domain\Content\Entities;

/**
 * The fixed brand copy of the landing page — the words that are the firm's
 * positioning rather than its catalogue. Everything here is authored, versioned
 * and reviewed by the partners, so it lives as a single immutable object rather
 * than as editable rows.
 */
final readonly class Narrative
{
    /**
     * @param  list<string>  $headline  the display headline, one entry per rendered line
     * @param  list<array{label:string,href:string}>  $navigation
     * @param  array{label:string,href:string}  $primaryCta
     * @param  array{label:string,href:string}  $secondaryCta
     */
    public function __construct(
        public string $company,
        public string $tagline,
        public string $eyebrow,
        public array $headline,
        public string $accentLine,
        public string $intro,
        public array $primaryCta,
        public array $secondaryCta,
        public string $creedTitle,
        public string $creedBody,
        public string $capabilitiesEyebrow,
        public string $capabilitiesHeading,
        public string $sectorsHeading,
        public string $reachHeading,
        public string $reachBody,
        public array $reachCta,
        public string $engagementHeading,
        public string $closingHeading,
        public string $closingSupport,
        public array $closingCta,
        public array $navigation,
        public string $copyright,
    ) {}

    public function toArray(): array
    {
        return [
            'company' => $this->company,
            'tagline' => $this->tagline,
            'hero' => [
                'eyebrow' => $this->eyebrow,
                'headline' => $this->headline,
                'accent_line' => $this->accentLine,
                'intro' => $this->intro,
                'primary_cta' => $this->primaryCta,
                'secondary_cta' => $this->secondaryCta,
            ],
            'creed' => [
                'title' => $this->creedTitle,
                'body' => $this->creedBody,
            ],
            'capabilities' => [
                'eyebrow' => $this->capabilitiesEyebrow,
                'heading' => $this->capabilitiesHeading,
            ],
            'sectors' => ['heading' => $this->sectorsHeading],
            'reach' => [
                'heading' => $this->reachHeading,
                'body' => $this->reachBody,
                'cta' => $this->reachCta,
            ],
            'engagement' => ['heading' => $this->engagementHeading],
            'closing' => [
                'heading' => $this->closingHeading,
                'support' => $this->closingSupport,
                'cta' => $this->closingCta,
            ],
            'navigation' => $this->navigation,
            'copyright' => $this->copyright,
        ];
    }
}
