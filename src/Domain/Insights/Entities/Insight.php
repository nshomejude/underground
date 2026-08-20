<?php

declare(strict_types=1);

namespace Domain\Insights\Entities;

use DateTimeImmutable;
use Domain\Shared\ValueObjects\Slug;

/**
 * A published piece of thinking from the think tank practice.
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
    ) {
    }

    public function isPublished(?DateTimeImmutable $now = null): bool
    {
        return $this->publishedAt !== null
            && $this->publishedAt <= ($now ?? new DateTimeImmutable());
    }

    public function readingMinutes(): int
    {
        return max(1, (int) ceil(str_word_count(strip_tags($this->body)) / 200));
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
        ];
    }
}
