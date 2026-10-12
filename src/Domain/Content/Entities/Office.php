<?php

declare(strict_types=1);

namespace Domain\Content\Entities;

use Domain\Content\ValueObjects\OfficeStatus;
use Domain\Content\ValueObjects\RelationshipType;
use Domain\Shared\ValueObjects\Slug;

/**
 * One location, or regional coverage area, in Underground's alliance
 * network — shown on /global-reach, /global-alliance and /locations.
 *
 * Replaces the old hardcoded App\Http\Controllers\OfficeDirectory array.
 * Every entry carries an explicit status and relationshipType so the
 * site never implies an owned/staffed branch, or an employee/subsidiary
 * partner, unless status is literally ActiveOffice. Only the firm's one
 * directly-operated location is $isHeadquarters.
 */
final readonly class Office
{
    public function __construct(
        public Slug $slug,
        public string $name,
        public string $region,
        public ?string $address,
        public ?string $email,
        public ?string $phone,
        public string $blurb,
        public ?float $lat,
        public ?float $lng,
        /** @var list<string> */
        public array $countries,
        public OfficeStatus $status,
        public RelationshipType $relationshipType,
        public bool $isHeadquarters,
        public int $position,
    ) {}

    /**
     * Whether this entry has a real point to plot on the interactive
     * globe. Regional/continental coverage entries without a confirmed
     * city are listed on /locations but never invented a position on
     * the map.
     */
    public function hasCoordinates(): bool
    {
        return $this->lat !== null && $this->lng !== null;
    }

    public function toArray(): array
    {
        return [
            'slug' => $this->slug->value,
            'name' => $this->name,
            'region' => $this->region,
            'address' => $this->address,
            'email' => $this->email,
            'phone' => $this->phone,
            'blurb' => $this->blurb,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'countries' => $this->countries,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'relationship_type' => $this->relationshipType->value,
            'relationship_type_label' => $this->relationshipType->label(),
            'is_headquarters' => $this->isHeadquarters,
            'position' => $this->position,
        ];
    }
}
