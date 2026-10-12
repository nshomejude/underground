<?php

declare(strict_types=1);

namespace Tests\Unit\Content;

use Domain\Content\Entities\MediaItem;
use Domain\Content\ValueObjects\MediaItemStatus;
use Domain\Content\ValueObjects\MediaType;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Support\Carbon;
use Tests\TestCase;

final class MediaItemTest extends TestCase
{
    public function test_to_array_exposes_the_expected_shape(): void
    {
        $item = new MediaItem(
            slug: Slug::fromString('submarine-diplomacy-the-discreet-channel'),
            type: MediaType::Podcast,
            title: 'Submarine Diplomacy: The Discreet Channel',
            description: 'A prepared, confidential channel between principals.',
            date: Carbon::parse('2026-08-05'),
            speaker: 'Tony Smith, Founder & Managing Partner',
            status: MediaItemStatus::Published,
            position: 1,
        );

        $data = $item->toArray();

        $this->assertSame('submarine-diplomacy-the-discreet-channel', $data['slug']);
        $this->assertSame('podcast', $data['type']);
        $this->assertSame('Tony Smith, Founder & Managing Partner', $data['speaker']);
        $this->assertSame('published', $data['status']);
        $this->assertSame('Published', $data['status_label']);
        $this->assertSame(1, $data['position']);
    }

    public function test_date_and_speaker_may_be_null(): void
    {
        $item = new MediaItem(
            slug: Slug::fromString('underground-magazine-issue-04-transition-states'),
            type: MediaType::Magazine,
            title: 'Underground Magazine — Issue 04: Transition States',
            description: 'Institutions in transition.',
            date: null,
            speaker: null,
            status: MediaItemStatus::Upcoming,
            position: 4,
        );

        $data = $item->toArray();

        $this->assertNull($data['date']);
        $this->assertNull($data['speaker']);
        $this->assertSame('upcoming', $data['status']);
    }

    public function test_media_type_labels(): void
    {
        $this->assertSame('Livestream', MediaType::Livestream->label());
        $this->assertSame('Podcast', MediaType::Podcast->label());
        $this->assertSame('Underground Magazine', MediaType::Magazine->label());
    }

    public function test_media_item_status_labels(): void
    {
        $this->assertSame('Upcoming', MediaItemStatus::Upcoming->label());
        $this->assertSame('Published', MediaItemStatus::Published->label());
        $this->assertSame('Archived', MediaItemStatus::Archived->label());
    }
}
