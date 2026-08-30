<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Repositories;

use Domain\Engagement\Entities\ConfidentialInquiry;
use Domain\Engagement\Repositories\InquiryRepository;
use Domain\Engagement\ValueObjects\InquiryReference;
use Domain\Engagement\ValueObjects\InquiryStatus;
use Domain\Engagement\ValueObjects\InterestArea;
use Domain\Shared\ValueObjects\EmailAddress;
use Infrastructure\Persistence\Eloquent\Models\InquiryRecord;

final class EloquentInquiryRepository implements InquiryRepository
{
    public function save(ConfidentialInquiry $inquiry): void
    {
        InquiryRecord::query()->updateOrCreate(
            ['reference' => $inquiry->reference->value],
            [
                'name' => $inquiry->name,
                'organisation' => $inquiry->organisation,
                'email' => $inquiry->email->value,
                'phone' => $inquiry->phone,
                'country' => $inquiry->country,
                'interest' => $inquiry->interest->value,
                'brief' => $inquiry->brief,
                'status' => $inquiry->status()->value,
                'submitted_at' => $inquiry->submittedAt,
            ],
        );
    }

    public function findByReference(InquiryReference $reference): ?ConfidentialInquiry
    {
        $record = InquiryRecord::query()->where('reference', $reference->value)->first();

        return $record === null ? null : $this->toEntity($record);
    }

    private function toEntity(InquiryRecord $record): ConfidentialInquiry
    {
        return ConfidentialInquiry::reconstitute(
            reference: InquiryReference::fromString($record->reference),
            name: $record->name,
            organisation: $record->organisation,
            email: EmailAddress::fromString($record->email),
            phone: $record->phone,
            country: $record->country,
            interest: InterestArea::from($record->interest),
            brief: $record->brief,
            submittedAt: $record->submitted_at,
            status: InquiryStatus::from($record->status),
        );
    }
}
