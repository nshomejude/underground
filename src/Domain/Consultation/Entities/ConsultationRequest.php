<?php

declare(strict_types=1);

namespace Domain\Consultation\Entities;

use DateTimeImmutable;
use Domain\Consultation\Exceptions\IllegalConsultationTransition;
use Domain\Consultation\ValueObjects\BudgetRange;
use Domain\Consultation\ValueObjects\ConsultationReference;
use Domain\Consultation\ValueObjects\ConsultationSector;
use Domain\Consultation\ValueObjects\ConsultationStatus;
use Domain\Consultation\ValueObjects\MaturityStage;
use Domain\Shared\Exceptions\DomainException;
use Domain\Shared\ValueObjects\EmailAddress;

/**
 * The aggregate root of the Consultation context: a free qualification
 * request made through the Underground Consultation Desk. Sibling to
 * Domain\Engagement\Entities\ConfidentialInquiry — that aggregate is an
 * approach to engage the firm on a mandate; this one is the lighter-weight,
 * no-cost intake that replaces an unstructured exploratory call, reviewed by
 * staff before any paid consultation is offered. Booking and payment for
 * that paid consultation are out of scope here by design (see
 * ConsultationStatus's class doc).
 */
final class ConsultationRequest
{
    private const MINIMUM_PROJECT_DESCRIPTION_LENGTH = 20;

    private const MINIMUM_NEED_DESCRIPTION_LENGTH = 20;

    private function __construct(
        public readonly ConsultationReference $reference,
        public readonly string $name,
        public readonly ?string $organisation,
        public readonly ?string $role,
        public readonly EmailAddress $email,
        public readonly ?string $country,
        public readonly ConsultationSector $sector,
        public readonly MaturityStage $maturityStage,
        public readonly string $projectDescription,
        public readonly string $needDescription,
        public readonly BudgetRange $budgetRange,
        public readonly bool $hasSupportingDocuments,
        public readonly DateTimeImmutable $submittedAt,
        private ConsultationStatus $status,
    ) {}

    public static function submit(
        string $name,
        ?string $organisation,
        ?string $role,
        EmailAddress $email,
        ?string $country,
        ConsultationSector $sector,
        MaturityStage $maturityStage,
        string $projectDescription,
        string $needDescription,
        BudgetRange $budgetRange,
        bool $hasSupportingDocuments,
        ?DateTimeImmutable $submittedAt = null,
        ?ConsultationReference $reference = null,
    ): self {
        $name = trim($name);
        $projectDescription = trim($projectDescription);
        $needDescription = trim($needDescription);

        if ($name === '') {
            throw new DomainException('A consultation request must carry a name.');
        }

        if (mb_strlen($projectDescription) < self::MINIMUM_PROJECT_DESCRIPTION_LENGTH) {
            throw new DomainException(sprintf(
                'A project description must be at least %d characters so it can be reviewed.',
                self::MINIMUM_PROJECT_DESCRIPTION_LENGTH,
            ));
        }

        if (mb_strlen($needDescription) < self::MINIMUM_NEED_DESCRIPTION_LENGTH) {
            throw new DomainException(sprintf(
                'A need description must be at least %d characters so it can be reviewed.',
                self::MINIMUM_NEED_DESCRIPTION_LENGTH,
            ));
        }

        $submittedAt ??= new DateTimeImmutable;

        return new self(
            reference: $reference ?? ConsultationReference::generate((int) $submittedAt->format('Y')),
            name: $name,
            organisation: self::nullIfBlank($organisation),
            role: self::nullIfBlank($role),
            email: $email,
            country: self::nullIfBlank($country),
            sector: $sector,
            maturityStage: $maturityStage,
            projectDescription: $projectDescription,
            needDescription: $needDescription,
            budgetRange: $budgetRange,
            hasSupportingDocuments: $hasSupportingDocuments,
            submittedAt: $submittedAt,
            status: ConsultationStatus::Submitted,
        );
    }

    /** Rehydrate an aggregate that already exists in storage. */
    public static function reconstitute(
        ConsultationReference $reference,
        string $name,
        ?string $organisation,
        ?string $role,
        EmailAddress $email,
        ?string $country,
        ConsultationSector $sector,
        MaturityStage $maturityStage,
        string $projectDescription,
        string $needDescription,
        BudgetRange $budgetRange,
        bool $hasSupportingDocuments,
        DateTimeImmutable $submittedAt,
        ConsultationStatus $status,
    ): self {
        return new self(
            $reference, $name, $organisation, $role, $email, $country,
            $sector, $maturityStage, $projectDescription, $needDescription,
            $budgetRange, $hasSupportingDocuments, $submittedAt, $status,
        );
    }

    public function status(): ConsultationStatus
    {
        return $this->status;
    }

    public function transitionTo(ConsultationStatus $target): void
    {
        if (! $this->status->canTransitionTo($target)) {
            throw IllegalConsultationTransition::between($this->status, $target);
        }

        $this->status = $target;
    }

    public function toArray(): array
    {
        return [
            'reference' => $this->reference->value,
            'name' => $this->name,
            'organisation' => $this->organisation,
            'role' => $this->role,
            'email' => $this->email->value,
            'country' => $this->country,
            'sector' => $this->sector->value,
            'sector_label' => $this->sector->label(),
            'maturity_stage' => $this->maturityStage->value,
            'maturity_stage_label' => $this->maturityStage->label(),
            'project_description' => $this->projectDescription,
            'need_description' => $this->needDescription,
            'budget_range' => $this->budgetRange->value,
            'budget_range_label' => $this->budgetRange->label(),
            'has_supporting_documents' => $this->hasSupportingDocuments,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'submitted_at' => $this->submittedAt->format(DATE_ATOM),
        ];
    }

    private static function nullIfBlank(?string $value): ?string
    {
        $value = $value === null ? null : trim($value);

        return $value === '' ? null : $value;
    }
}
