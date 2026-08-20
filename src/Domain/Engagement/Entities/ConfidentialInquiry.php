<?php

declare(strict_types=1);

namespace Domain\Engagement\Entities;

use DateTimeImmutable;
use Domain\Engagement\Exceptions\IllegalInquiryTransition;
use Domain\Engagement\ValueObjects\InquiryReference;
use Domain\Engagement\ValueObjects\InquiryStatus;
use Domain\Engagement\ValueObjects\InterestArea;
use Domain\Shared\Exceptions\DomainException;
use Domain\Shared\ValueObjects\EmailAddress;

/**
 * The aggregate root of the Engagement context: an approach made through the
 * "Start a confidential conversation" form.
 */
final class ConfidentialInquiry
{
    private const MINIMUM_BRIEF_LENGTH = 20;

    private function __construct(
        public readonly InquiryReference $reference,
        public readonly string $name,
        public readonly ?string $organisation,
        public readonly EmailAddress $email,
        public readonly ?string $phone,
        public readonly ?string $country,
        public readonly InterestArea $interest,
        public readonly string $brief,
        public readonly DateTimeImmutable $submittedAt,
        private InquiryStatus $status,
    ) {
    }

    public static function submit(
        string $name,
        ?string $organisation,
        EmailAddress $email,
        ?string $phone,
        ?string $country,
        InterestArea $interest,
        string $brief,
        ?DateTimeImmutable $submittedAt = null,
        ?InquiryReference $reference = null,
    ): self {
        $name = trim($name);
        $brief = trim($brief);

        if ($name === '') {
            throw new DomainException('An inquiry must carry a name.');
        }

        if (mb_strlen($brief) < self::MINIMUM_BRIEF_LENGTH) {
            throw new DomainException(sprintf(
                'A brief must be at least %d characters so it can be triaged.',
                self::MINIMUM_BRIEF_LENGTH,
            ));
        }

        $submittedAt ??= new DateTimeImmutable();

        return new self(
            reference: $reference ?? InquiryReference::generate((int) $submittedAt->format('Y')),
            name: $name,
            organisation: self::nullIfBlank($organisation),
            email: $email,
            phone: self::nullIfBlank($phone),
            country: self::nullIfBlank($country),
            interest: $interest,
            brief: $brief,
            submittedAt: $submittedAt,
            status: InquiryStatus::Received,
        );
    }

    /** Rehydrate an aggregate that already exists in storage. */
    public static function reconstitute(
        InquiryReference $reference,
        string $name,
        ?string $organisation,
        EmailAddress $email,
        ?string $phone,
        ?string $country,
        InterestArea $interest,
        string $brief,
        DateTimeImmutable $submittedAt,
        InquiryStatus $status,
    ): self {
        return new self(
            $reference, $name, $organisation, $email, $phone,
            $country, $interest, $brief, $submittedAt, $status,
        );
    }

    public function status(): InquiryStatus
    {
        return $this->status;
    }

    public function transitionTo(InquiryStatus $target): void
    {
        if (! $this->status->canTransitionTo($target)) {
            throw IllegalInquiryTransition::between($this->status, $target);
        }

        $this->status = $target;
    }

    /**
     * Crisis and undisclosed approaches skip the intake queue; so does anything
     * from a state or sovereign domain, which is always partner business.
     */
    public function needsPartnerTriage(): bool
    {
        return $this->interest->requiresPartnerTriage()
            || str_ends_with($this->email->domain(), '.gov')
            || str_ends_with($this->email->domain(), '.mil');
    }

    public function toArray(): array
    {
        return [
            'reference' => $this->reference->value,
            'name' => $this->name,
            'organisation' => $this->organisation,
            'email' => $this->email->value,
            'phone' => $this->phone,
            'country' => $this->country,
            'interest' => $this->interest->value,
            'interest_label' => $this->interest->label(),
            'brief' => $this->brief,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'partner_triage' => $this->needsPartnerTriage(),
            'submitted_at' => $this->submittedAt->format(DATE_ATOM),
        ];
    }

    private static function nullIfBlank(?string $value): ?string
    {
        $value = $value === null ? null : trim($value);

        return $value === '' ? null : $value;
    }
}
