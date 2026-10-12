<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Repositories;

use Domain\Consultation\Entities\ConsultationRequest;
use Domain\Consultation\Repositories\ConsultationRequestRepository;
use Domain\Consultation\ValueObjects\BudgetRange;
use Domain\Consultation\ValueObjects\ConsultationReference;
use Domain\Consultation\ValueObjects\ConsultationSector;
use Domain\Consultation\ValueObjects\ConsultationStatus;
use Domain\Consultation\ValueObjects\MaturityStage;
use Domain\Shared\ValueObjects\EmailAddress;
use Infrastructure\Persistence\Eloquent\Models\ConsultationRequestRecord;

final class EloquentConsultationRequestRepository implements ConsultationRequestRepository
{
    public function save(ConsultationRequest $request): void
    {
        ConsultationRequestRecord::query()->updateOrCreate(
            ['reference' => $request->reference->value],
            [
                'name' => $request->name,
                'organisation' => $request->organisation,
                'role' => $request->role,
                'email' => $request->email->value,
                'country' => $request->country,
                'sector' => $request->sector->value,
                'maturity_stage' => $request->maturityStage->value,
                'project_description' => $request->projectDescription,
                'need_description' => $request->needDescription,
                'budget_range' => $request->budgetRange->value,
                'has_supporting_documents' => $request->hasSupportingDocuments,
                'status' => $request->status()->value,
                'submitted_at' => $request->submittedAt,
            ],
        );
    }

    public function findByReference(ConsultationReference $reference): ?ConsultationRequest
    {
        $record = ConsultationRequestRecord::query()->where('reference', $reference->value)->first();

        return $record === null ? null : $this->toEntity($record);
    }

    public function all(): array
    {
        return ConsultationRequestRecord::query()
            ->orderByDesc('submitted_at')
            ->get()
            ->map($this->toEntity(...))
            ->all();
    }

    private function toEntity(ConsultationRequestRecord $record): ConsultationRequest
    {
        return ConsultationRequest::reconstitute(
            reference: ConsultationReference::fromString($record->reference),
            name: $record->name,
            organisation: $record->organisation,
            role: $record->role,
            email: EmailAddress::fromString($record->email),
            country: $record->country,
            sector: ConsultationSector::from($record->sector),
            maturityStage: MaturityStage::from($record->maturity_stage),
            projectDescription: $record->project_description,
            needDescription: $record->need_description,
            budgetRange: BudgetRange::from($record->budget_range),
            hasSupportingDocuments: (bool) $record->has_supporting_documents,
            submittedAt: $record->submitted_at,
            status: ConsultationStatus::from($record->status),
        );
    }
}
