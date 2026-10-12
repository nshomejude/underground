<?php

declare(strict_types=1);

namespace Application\Insights\Queries;

use DateTimeImmutable;
use Domain\Insights\Entities\Insight;
use Domain\Insights\Repositories\InsightRepository;
use Domain\Shared\ValueObjects\Slug;

/**
 * Reads behind both the legacy Insights pages and the Reports Hub that
 * supersedes them -- the Reports Hub's filtering/sorting lives here rather
 * than in a parallel query class, since it is the same underlying content.
 */
final readonly class ListInsights
{
    /** The category Reports Hub's "Strategic Notes" view filters to. */
    public const string STRATEGIC_NOTES_CATEGORY = 'Strategic Notes';

    public function __construct(private InsightRepository $insights) {}

    /** @return list<Insight> */
    public function __invoke(?int $limit = null): array
    {
        return $this->insights->published($limit);
    }

    public function bySlug(string $slug): ?Insight
    {
        return $this->insights->findBySlug(Slug::fromString($slug));
    }

    /**
     * Published reports, optionally narrowed by category/country/sector and
     * sorted. Filtering is applied in memory over the full published set so
     * a filter never silently drops matches behind an earlier limit.
     *
     * @return list<Insight>
     */
    public function filtered(
        ?string $category = null,
        ?string $country = null,
        ?string $sector = null,
        string $sort = 'newest',
    ): array {
        $reports = array_values(array_filter(
            $this->insights->published(),
            fn (Insight $report): bool => ($category === null || $report->category === $category)
                && ($country === null || $report->country === $country)
                && ($sector === null || $report->sector === $sector),
        ));

        usort($reports, $this->comparator($sort));

        return $reports;
    }

    /** @return list<Insight> published reports categorised as Strategic Notes */
    public function strategicNotes(string $sort = 'newest'): array
    {
        return $this->filtered(category: self::STRATEGIC_NOTES_CATEGORY, sort: $sort);
    }

    /** @return list<string> distinct categories among published reports, alphabetical */
    public function categories(): array
    {
        return $this->distinctValues(fn (Insight $report): string => $report->category);
    }

    /** @return list<string> distinct countries among published reports, alphabetical */
    public function countries(): array
    {
        return $this->distinctValues(fn (Insight $report): string => $report->country);
    }

    /** @return list<string> distinct sectors among published reports, alphabetical */
    public function sectors(): array
    {
        return $this->distinctValues(fn (Insight $report): string => $report->sector);
    }

    /** @return callable(Insight, Insight): int */
    private function comparator(string $sort): callable
    {
        return match ($sort) {
            'oldest' => fn (Insight $a, Insight $b): int => $this->publishedAtOrEpoch($a) <=> $this->publishedAtOrEpoch($b),
            'title' => fn (Insight $a, Insight $b): int => strcasecmp($a->title, $b->title),
            default => fn (Insight $a, Insight $b): int => $this->publishedAtOrEpoch($b) <=> $this->publishedAtOrEpoch($a),
        };
    }

    private function publishedAtOrEpoch(Insight $report): DateTimeImmutable
    {
        return $report->publishedAt ?? new DateTimeImmutable('@0');
    }

    /** @param callable(Insight): string $pluck @return list<string> */
    private function distinctValues(callable $pluck): array
    {
        $values = array_values(array_unique(array_map($pluck, $this->insights->published())));
        sort($values);

        return $values;
    }
}
