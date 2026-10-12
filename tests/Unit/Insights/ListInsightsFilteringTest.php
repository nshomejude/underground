<?php

declare(strict_types=1);

namespace Tests\Unit\Insights;

use Application\Insights\Queries\ListInsights;
use DateTimeImmutable;
use Domain\Insights\Entities\Insight;
use Domain\Insights\Repositories\InsightRepository;
use Domain\Insights\ValueObjects\AccessLevel;
use Domain\Shared\ValueObjects\Slug;
use Tests\TestCase;

final class ListInsightsFilteringTest extends TestCase
{
    private function reportsHub(): ListInsights
    {
        $published = [
            new Insight(
                slug: Slug::fromString('oldest-note'),
                title: 'Oldest Note',
                category: 'Strategic Notes',
                excerpt: 'An excerpt.',
                body: 'A body.',
                publishedAt: new DateTimeImmutable('-3 months'),
                author: 'Tony Smith',
                country: 'Global',
                sector: 'Strategic Advisory',
                accessLevel: AccessLevel::MembersOnly,
            ),
            new Insight(
                slug: Slug::fromString('newest-brief'),
                title: 'Newest Brief',
                category: 'Geopolitics',
                excerpt: 'An excerpt.',
                body: 'A body.',
                publishedAt: new DateTimeImmutable('-1 week'),
                author: 'Yumi Castellanos',
                country: 'United States',
                sector: 'Finance & Investments',
                accessLevel: AccessLevel::PublicAccess,
            ),
            new Insight(
                slug: Slug::fromString('middle-note'),
                title: 'Middle Note',
                category: 'Strategic Notes',
                excerpt: 'An excerpt.',
                body: 'A body.',
                publishedAt: new DateTimeImmutable('-1 month'),
                author: 'Marcus Thane',
                country: 'Global',
                sector: 'Infrastructure & Transportation',
                accessLevel: AccessLevel::PublicAccess,
            ),
        ];

        $unpublished = [
            new Insight(
                slug: Slug::fromString('draft-note'),
                title: 'Draft Note',
                category: 'Strategic Notes',
                excerpt: 'An excerpt.',
                body: 'A body.',
                publishedAt: null,
            ),
        ];

        return new ListInsights(new InMemoryInsightRepositoryForTesting([...$published, ...$unpublished], $published));
    }

    public function test_filtered_with_no_filters_returns_all_published_newest_first(): void
    {
        $slugs = array_map(fn (Insight $i) => $i->slug->value, $this->reportsHub()->filtered());

        $this->assertSame(['newest-brief', 'middle-note', 'oldest-note'], $slugs);
    }

    public function test_filtered_by_category_only_matches_that_category(): void
    {
        $slugs = array_map(fn (Insight $i) => $i->slug->value, $this->reportsHub()->filtered(category: 'Strategic Notes'));

        $this->assertSame(['middle-note', 'oldest-note'], $slugs);
    }

    public function test_filtered_by_country_only_matches_that_country(): void
    {
        $slugs = array_map(fn (Insight $i) => $i->slug->value, $this->reportsHub()->filtered(country: 'United States'));

        $this->assertSame(['newest-brief'], $slugs);
    }

    public function test_filtered_by_sector_only_matches_that_sector(): void
    {
        $slugs = array_map(fn (Insight $i) => $i->slug->value, $this->reportsHub()->filtered(sector: 'Finance & Investments'));

        $this->assertSame(['newest-brief'], $slugs);
    }

    public function test_filtered_combines_filters_with_and_semantics(): void
    {
        $slugs = array_map(
            fn (Insight $i) => $i->slug->value,
            $this->reportsHub()->filtered(category: 'Strategic Notes', country: 'Global', sector: 'Strategic Advisory'),
        );

        $this->assertSame(['oldest-note'], $slugs);
    }

    public function test_filtered_sorts_oldest_first_when_asked(): void
    {
        $slugs = array_map(fn (Insight $i) => $i->slug->value, $this->reportsHub()->filtered(sort: 'oldest'));

        $this->assertSame(['oldest-note', 'middle-note', 'newest-brief'], $slugs);
    }

    public function test_filtered_sorts_by_title_when_asked(): void
    {
        $slugs = array_map(fn (Insight $i) => $i->slug->value, $this->reportsHub()->filtered(sort: 'title'));

        $this->assertSame(['middle-note', 'newest-brief', 'oldest-note'], $slugs);
    }

    public function test_strategic_notes_only_returns_that_category(): void
    {
        $slugs = array_map(fn (Insight $i) => $i->slug->value, $this->reportsHub()->strategicNotes());

        $this->assertSame(['middle-note', 'oldest-note'], $slugs);
    }

    public function test_categories_countries_and_sectors_are_distinct_and_sorted(): void
    {
        $reportsHub = $this->reportsHub();

        $this->assertSame(['Geopolitics', 'Strategic Notes'], $reportsHub->categories());
        $this->assertSame(['Global', 'United States'], $reportsHub->countries());
        $this->assertSame(['Finance & Investments', 'Infrastructure & Transportation', 'Strategic Advisory'], $reportsHub->sectors());
    }
}

/**
 * A minimal in-memory InsightRepository so the Application-layer filtering
 * logic can be tested without a database.
 */
final readonly class InMemoryInsightRepositoryForTesting implements InsightRepository
{
    /**
     * @param  list<Insight>  $all
     * @param  list<Insight>  $published
     */
    public function __construct(private array $all, private array $published) {}

    public function published(?int $limit = null): array
    {
        return $limit === null ? $this->published : array_slice($this->published, 0, $limit);
    }

    public function all(): array
    {
        return $this->all;
    }

    public function findBySlug(Slug $slug): ?Insight
    {
        foreach ($this->all as $insight) {
            if ($insight->slug->equals($slug)) {
                return $insight;
            }
        }

        return null;
    }

    public function save(Insight $insight): void
    {
        // Not needed for these tests.
    }

    public function delete(Slug $slug): void
    {
        // Not needed for these tests.
    }
}
