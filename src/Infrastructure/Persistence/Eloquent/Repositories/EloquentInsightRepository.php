<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Repositories;

use Domain\Insights\Entities\Insight;
use Domain\Insights\Repositories\InsightRepository;
use Domain\Shared\ValueObjects\Slug;
use Infrastructure\Persistence\Eloquent\Models\InsightRecord;

final class EloquentInsightRepository implements InsightRepository
{
    public function published(?int $limit = null): array
    {
        return InsightRecord::query()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->when($limit !== null, fn ($query) => $query->limit($limit))
            ->get()
            ->map($this->toEntity(...))
            ->all();
    }

    public function findBySlug(Slug $slug): ?Insight
    {
        $record = InsightRecord::query()->where('slug', $slug->value)->first();

        return $record === null ? null : $this->toEntity($record);
    }

    private function toEntity(InsightRecord $record): Insight
    {
        return new Insight(
            slug: Slug::fromString($record->slug),
            title: $record->title,
            category: $record->category,
            excerpt: $record->excerpt,
            body: $record->body,
            publishedAt: $record->published_at,
        );
    }
}
