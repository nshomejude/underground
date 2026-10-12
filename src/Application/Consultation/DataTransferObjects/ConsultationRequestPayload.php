<?php

declare(strict_types=1);

namespace Application\Consultation\DataTransferObjects;

use Domain\Consultation\ValueObjects\BudgetRange;
use Domain\Consultation\ValueObjects\ConsultationSector;
use Domain\Consultation\ValueObjects\MaturityStage;
use Domain\Shared\ValueObjects\EmailAddress;

/**
 * Validated, transport-agnostic input for the submit-consultation-request
 * use case.
 */
final readonly class ConsultationRequestPayload
{
    public function __construct(
        public string $name,
        public ?string $organisation,
        public ?string $role,
        public EmailAddress $email,
        public ?string $country,
        public ConsultationSector $sector,
        public MaturityStage $maturityStage,
        public string $projectDescription,
        public string $needDescription,
        public BudgetRange $budgetRange,
        public bool $hasSupportingDocuments,
    ) {}

    /** @param array<string,mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            name: (string) ($data['name'] ?? ''),
            organisation: isset($data['organisation']) ? (string) $data['organisation'] : null,
            role: isset($data['role']) ? (string) $data['role'] : null,
            email: EmailAddress::fromString((string) ($data['email'] ?? '')),
            country: isset($data['country']) ? (string) $data['country'] : null,
            sector: ConsultationSector::tryFrom((string) ($data['sector'] ?? '')) ?? ConsultationSector::Other,
            maturityStage: MaturityStage::tryFrom((string) ($data['maturity_stage'] ?? '')) ?? MaturityStage::Idea,
            projectDescription: (string) ($data['project_description'] ?? ''),
            needDescription: (string) ($data['need_description'] ?? ''),
            budgetRange: BudgetRange::tryFrom((string) ($data['budget_range'] ?? '')) ?? BudgetRange::Undisclosed,
            hasSupportingDocuments: filter_var($data['has_supporting_documents'] ?? false, FILTER_VALIDATE_BOOL),
        );
    }
}
