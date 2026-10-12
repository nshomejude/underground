<?php

declare(strict_types=1);

namespace Tests\Unit\Content;

use Domain\Content\Entities\Office;
use Domain\Content\ValueObjects\OfficeStatus;
use Domain\Content\ValueObjects\RelationshipType;
use Domain\Shared\ValueObjects\Slug;
use Tests\TestCase;

final class OfficeTest extends TestCase
{
    public function test_has_coordinates_is_true_when_both_lat_and_lng_are_present(): void
    {
        $office = $this->makeOffice(lat: 38.9072, lng: -77.0369);

        $this->assertTrue($office->hasCoordinates());
    }

    public function test_has_coordinates_is_false_when_either_is_missing(): void
    {
        $this->assertFalse($this->makeOffice(lat: null, lng: -77.0369)->hasCoordinates());
        $this->assertFalse($this->makeOffice(lat: 38.9072, lng: null)->hasCoordinates());
        $this->assertFalse($this->makeOffice(lat: null, lng: null)->hasCoordinates());
    }

    public function test_to_array_exposes_the_expected_shape_including_status_and_relationship_labels(): void
    {
        $office = $this->makeOffice(
            status: OfficeStatus::PartnerCoverage,
            relationshipType: RelationshipType::Partnership,
            countries: ['Cameroon'],
        );

        $data = $office->toArray();

        $this->assertSame('partner_coverage', $data['status']);
        $this->assertSame('Partner Coverage', $data['status_label']);
        $this->assertSame('partnership', $data['relationship_type']);
        $this->assertSame('Partnership', $data['relationship_type_label']);
        $this->assertSame(['Cameroon'], $data['countries']);
        $this->assertFalse($data['is_headquarters']);
    }

    public function test_an_active_office_relationship_and_status_round_trip(): void
    {
        $office = $this->makeOffice(
            status: OfficeStatus::ActiveOffice,
            relationshipType: RelationshipType::Independent,
            isHeadquarters: true,
        );

        $this->assertTrue($office->isHeadquarters);
        $this->assertSame(OfficeStatus::ActiveOffice, $office->status);
        $this->assertSame(RelationshipType::Independent, $office->relationshipType);
    }

    private function makeOffice(
        ?float $lat = 38.9072,
        ?float $lng = -77.0369,
        OfficeStatus $status = OfficeStatus::PartnerCoverage,
        RelationshipType $relationshipType = RelationshipType::Partnership,
        bool $isHeadquarters = false,
        array $countries = [],
    ): Office {
        return new Office(
            slug: Slug::fromString('test-office'),
            name: 'Test Office',
            region: 'Nowhere',
            address: null,
            email: null,
            phone: null,
            blurb: 'A test office.',
            lat: $lat,
            lng: $lng,
            countries: $countries,
            status: $status,
            relationshipType: $relationshipType,
            isHeadquarters: $isHeadquarters,
            position: 1,
        );
    }
}
