<?php

declare(strict_types=1);

namespace Domain\Content\Entities;

use Domain\Shared\ValueObjects\Slug;

/**
 * A selected, anonymised past engagement — shown on /portfolio and, with
 * the full case-study field set below, on /team/case-studies (see
 * App\Http\Controllers\TeamController::caseStudies()). `summary` serves
 * as the case study's context and `outcome` as its demonstrable result;
 * realRole, deliverables, period and publicationAgreementNote are the
 * remaining fields the client brief asks a case study to carry.
 */
final readonly class PortfolioEngagement
{
    public function __construct(
        public Slug $slug,
        public string $sector,
        public string $title,
        public string $summary,
        public string $outcome,
        public int $position,
        public ?string $realRole = null,
        /** @var list<string> */
        public array $deliverables = [],
        public ?string $period = null,
        public ?string $publicationAgreementNote = null,
    ) {}

    public function toArray(): array
    {
        return [
            'slug' => $this->slug->value,
            'sector' => $this->sector,
            'title' => $this->title,
            'summary' => $this->summary,
            'outcome' => $this->outcome,
            'position' => $this->position,
            'real_role' => $this->realRole,
            'deliverables' => $this->deliverables,
            'period' => $this->period,
            'publication_agreement_note' => $this->publicationAgreementNote,
        ];
    }
}
