<?php

declare(strict_types=1);

namespace Domain\Content\Entities;

use Domain\Shared\ValueObjects\Slug;

/**
 * One of the disciplines Underground sells: "Government Affairs &
 * Institutional Relations", "Strategic Intelligence & Research", and so on.
 */
final readonly class Capability
{
    /**
     * @param  list<string>  $audiences  who this expertise serves
     * @param  list<string>  $deliverables  concrete outputs per mission
     * @param  string  $problemStatement  the problem this expertise addresses
     * @param  string  $method  how Underground approaches it
     * @param  ?string  $confirmedLead  a named lead, only when genuinely confirmed elsewhere
     * @param  ?string  $authorizedReference  a case study/reference, only when one genuinely exists
     * @param  string  $nextAction  the call-to-action line for this capability's detail page
     */
    public function __construct(
        public Slug $slug,
        public string $title,
        public string $summary,
        public string $icon,
        public int $position,
        public bool $isFeatured,
        public string $problemStatement,
        public array $audiences,
        public string $method,
        public array $deliverables,
        public ?string $confirmedLead,
        public ?string $authorizedReference,
        public string $nextAction,
    ) {}

    /**
     * The mobile layout shows the title on a single line, the desktop card
     * breaks it across two. The ampersand is the natural hinge.
     */
    public function titleLines(): array
    {
        $parts = explode(' & ', $this->title, 2);

        return count($parts) === 2
            ? [$parts[0].' &', $parts[1]]
            : [$this->title];
    }

    public function toArray(): array
    {
        return [
            'slug' => $this->slug->value,
            'title' => $this->title,
            'title_lines' => $this->titleLines(),
            'summary' => $this->summary,
            'icon' => $this->icon,
            'position' => $this->position,
            'featured' => $this->isFeatured,
            'problem_statement' => $this->problemStatement,
            'audiences' => $this->audiences,
            'method' => $this->method,
            'deliverables' => $this->deliverables,
            'confirmed_lead' => $this->confirmedLead,
            'authorized_reference' => $this->authorizedReference,
            'next_action' => $this->nextAction,
        ];
    }
}
