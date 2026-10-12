<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Repositories;

use Domain\Content\Entities\PortfolioEngagement;
use Domain\Content\Repositories\PortfolioEngagementRepository;
use Domain\Shared\ValueObjects\Slug;
use Infrastructure\Persistence\Eloquent\Models\PortfolioEngagementRecord;

final class EloquentPortfolioEngagementRepository implements PortfolioEngagementRepository
{
    public function all(): array
    {
        return PortfolioEngagementRecord::query()
            ->orderBy('position')
            ->get()
            ->map($this->toEntity(...))
            ->all();
    }

    public function findBySlug(Slug $slug): ?PortfolioEngagement
    {
        $record = PortfolioEngagementRecord::query()->where('slug', $slug->value)->first();

        return $record === null ? null : $this->toEntity($record);
    }

    public function save(PortfolioEngagement $engagement, ?Slug $originalSlug = null): void
    {
        PortfolioEngagementRecord::query()->updateOrCreate(
            ['slug' => ($originalSlug ?? $engagement->slug)->value],
            [
                'slug' => $engagement->slug->value,
                'sector' => $engagement->sector,
                'title' => $engagement->title,
                'summary' => $engagement->summary,
                'outcome' => $engagement->outcome,
                'position' => $engagement->position,
                'real_role' => $engagement->realRole,
                'deliverables' => $engagement->deliverables,
                'period' => $engagement->period,
                'publication_agreement_note' => $engagement->publicationAgreementNote,
            ],
        );
    }

    public function delete(Slug $slug): void
    {
        PortfolioEngagementRecord::query()->where('slug', $slug->value)->delete();
    }

    private function toEntity(PortfolioEngagementRecord $record): PortfolioEngagement
    {
        return new PortfolioEngagement(
            slug: Slug::fromString($record->slug),
            sector: $record->sector,
            title: $record->title,
            summary: $record->summary,
            outcome: $record->outcome,
            position: $record->position,
            realRole: $record->real_role,
            deliverables: $record->deliverables ?? [],
            period: $record->period,
            publicationAgreementNote: $record->publication_agreement_note,
        );
    }
}
