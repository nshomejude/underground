<?php

declare(strict_types=1);

namespace Tests\Unit\Insights;

use DateTimeImmutable;
use Domain\Insights\Entities\Insight;
use Domain\Shared\ValueObjects\Slug;
use Tests\TestCase;

final class InsightTest extends TestCase
{
    public function test_is_published_is_true_when_published_at_is_in_the_past(): void
    {
        $insight = new Insight(
            slug: Slug::fromString('sovereign-risk-outlook'),
            title: 'Sovereign Risk Outlook',
            category: 'Geopolitics',
            excerpt: 'A short excerpt.',
            body: 'A short body.',
            publishedAt: new DateTimeImmutable('-1 day'),
        );

        $this->assertTrue($insight->isPublished());
    }

    public function test_is_published_is_false_when_published_at_is_in_the_future(): void
    {
        $insight = new Insight(
            slug: Slug::fromString('sovereign-risk-outlook'),
            title: 'Sovereign Risk Outlook',
            category: 'Geopolitics',
            excerpt: 'A short excerpt.',
            body: 'A short body.',
            publishedAt: new DateTimeImmutable('+1 day'),
        );

        $this->assertFalse($insight->isPublished());
    }

    public function test_is_published_is_false_when_published_at_is_null(): void
    {
        $insight = new Insight(
            slug: Slug::fromString('draft-outlook'),
            title: 'Draft Outlook',
            category: 'Geopolitics',
            excerpt: 'A short excerpt.',
            body: 'A short body.',
            publishedAt: null,
        );

        $this->assertFalse($insight->isPublished());
    }

    public function test_is_published_accepts_an_explicit_reference_time(): void
    {
        $insight = new Insight(
            slug: Slug::fromString('sovereign-risk-outlook'),
            title: 'Sovereign Risk Outlook',
            category: 'Geopolitics',
            excerpt: 'A short excerpt.',
            body: 'A short body.',
            publishedAt: new DateTimeImmutable('2026-06-01'),
        );

        $this->assertFalse($insight->isPublished(new DateTimeImmutable('2026-01-01')));
        $this->assertTrue($insight->isPublished(new DateTimeImmutable('2026-12-01')));
    }

    public function test_reading_minutes_is_at_least_one_for_a_short_body(): void
    {
        $insight = new Insight(
            slug: Slug::fromString('brief-note'),
            title: 'Brief Note',
            category: 'Geopolitics',
            excerpt: 'Short.',
            body: 'A very short body of a handful of words.',
            publishedAt: null,
        );

        $this->assertSame(1, $insight->readingMinutes());
    }

    public function test_reading_minutes_scales_with_word_count_and_strips_tags(): void
    {
        $body = '<p>'.implode(' ', array_fill(0, 450, 'word')).'</p>';

        $insight = new Insight(
            slug: Slug::fromString('long-form-analysis'),
            title: 'Long Form Analysis',
            category: 'Geopolitics',
            excerpt: 'A long excerpt.',
            body: $body,
            publishedAt: null,
        );

        // 450 words / 200 wpm = 2.25, rounded up to 3.
        $this->assertSame(3, $insight->readingMinutes());
    }

    public function test_to_array_exposes_the_expected_shape(): void
    {
        $publishedAt = new DateTimeImmutable('2026-06-01T10:00:00+00:00');

        $insight = new Insight(
            slug: Slug::fromString('sovereign-risk-outlook'),
            title: 'Sovereign Risk Outlook',
            category: 'Geopolitics',
            excerpt: 'A short excerpt.',
            body: 'A short body.',
            publishedAt: $publishedAt,
        );

        $data = $insight->toArray();

        $this->assertSame('sovereign-risk-outlook', $data['slug']);
        $this->assertSame('Geopolitics', $data['category']);
        $this->assertSame(1, $data['reading_minutes']);
        $this->assertSame($publishedAt->format(DATE_ATOM), $data['published_at']);
    }

    public function test_to_array_renders_a_null_published_at_as_null(): void
    {
        $insight = new Insight(
            slug: Slug::fromString('draft-outlook'),
            title: 'Draft Outlook',
            category: 'Geopolitics',
            excerpt: 'A short excerpt.',
            body: 'A short body.',
            publishedAt: null,
        );

        $this->assertNull($insight->toArray()['published_at']);
    }
}
