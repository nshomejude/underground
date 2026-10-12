<?php

declare(strict_types=1);

namespace Domain\Insights\Entities;

use DateTimeImmutable;
use Domain\Insights\ValueObjects\AccessLevel;
use Domain\Shared\ValueObjects\Slug;

/**
 * A published piece of thinking from the think tank practice -- presented
 * to visitors as the Reports Hub. Carries the richer editorial metadata
 * (author, country, sector, access level) the Reports Hub brief asked for,
 * as a straight extension of the same aggregate rather than a parallel
 * content type.
 */
final readonly class Insight
{
    public function __construct(
        public Slug $slug,
        public string $title,
        public string $category,
        public string $excerpt,
        public string $body,
        public ?DateTimeImmutable $publishedAt,
        public string $author = 'Underground Network Research',
        public string $country = 'Global',
        public string $sector = 'Cross-Sector',
        public AccessLevel $accessLevel = AccessLevel::PublicAccess,
    ) {}

    public function isPublished(?DateTimeImmutable $now = null): bool
    {
        return $this->publishedAt !== null
            && $this->publishedAt <= ($now ?? new DateTimeImmutable);
    }

    public function readingMinutes(): int
    {
        return max(1, (int) ceil(str_word_count(strip_tags($this->body)) / 200));
    }

    public function requiresMembership(): bool
    {
        return $this->accessLevel->requiresMembership();
    }

    public function toArray(): array
    {
        return [
            'slug' => $this->slug->value,
            'title' => $this->title,
            'category' => $this->category,
            'excerpt' => $this->excerpt,
            'body' => $this->body,
            'reading_minutes' => $this->readingMinutes(),
            'published_at' => $this->publishedAt?->format(DATE_ATOM),
            'author' => $this->author,
            'country' => $this->country,
            'sector' => $this->sector,
            'access_level' => $this->accessLevel->value,
            'access_level_label' => $this->accessLevel->label(),
        ];
    }
}
