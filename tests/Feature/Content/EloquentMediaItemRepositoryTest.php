<?php

declare(strict_types=1);

namespace Tests\Feature\Content;

use Domain\Content\Entities\MediaItem;
use Domain\Content\ValueObjects\MediaItemStatus;
use Domain\Content\ValueObjects\MediaType;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Infrastructure\Persistence\Eloquent\Models\MediaItemRecord;
use Infrastructure\Persistence\Eloquent\Repositories\EloquentMediaItemRepository;
use Tests\TestCase;

final class EloquentMediaItemRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_returns_media_items_ordered_by_type_then_position(): void
    {
        MediaItemRecord::factory()->create(['slug' => 'podcast-two', 'type' => 'podcast', 'position' => 2]);
        MediaItemRecord::factory()->create(['slug' => 'podcast-one', 'type' => 'podcast', 'position' => 1]);
        MediaItemRecord::factory()->create(['slug' => 'livestream-one', 'type' => 'livestream', 'position' => 1]);

        $repository = new EloquentMediaItemRepository;

        $this->assertSame(
            ['livestream-one', 'podcast-one', 'podcast-two'],
            array_map(fn ($item) => $item->slug->value, $repository->all()),
        );
    }

    public function test_find_by_slug_returns_the_matching_item(): void
    {
        MediaItemRecord::factory()->create([
            'slug' => 'submarine-diplomacy-the-discreet-channel',
            'type' => 'podcast',
            'title' => 'Submarine Diplomacy: The Discreet Channel',
            'description' => 'A prepared, confidential channel between principals.',
            'date' => '2026-08-05',
            'speaker' => 'Tony Smith, Founder & Managing Partner',
            'status' => 'published',
            'position' => 1,
        ]);

        $repository = new EloquentMediaItemRepository;

        $item = $repository->findBySlug(Slug::fromString('submarine-diplomacy-the-discreet-channel'));

        $this->assertNotNull($item);
        $this->assertSame(MediaType::Podcast, $item->type);
        $this->assertSame(MediaItemStatus::Published, $item->status);
        $this->assertSame('Tony Smith, Founder & Managing Partner', $item->speaker);
        $this->assertNotNull($item->date);
        $this->assertSame('2026-08-05', $item->date->toDateString());
    }

    public function test_find_by_slug_returns_null_for_a_missing_slug(): void
    {
        $repository = new EloquentMediaItemRepository;

        $this->assertNull($repository->findBySlug(Slug::fromString('does-not-exist')));
    }

    public function test_save_creates_a_new_item_with_a_null_date_and_speaker(): void
    {
        $repository = new EloquentMediaItemRepository;

        $repository->save(new MediaItem(
            slug: Slug::fromString('underground-magazine-issue-05'),
            type: MediaType::Magazine,
            title: 'Underground Magazine — Issue 05',
            description: 'A forthcoming issue.',
            date: null,
            speaker: null,
            status: MediaItemStatus::Upcoming,
            position: 5,
        ));

        $item = $repository->findBySlug(Slug::fromString('underground-magazine-issue-05'));

        $this->assertNotNull($item);
        $this->assertNull($item->date);
        $this->assertNull($item->speaker);
        $this->assertSame(MediaItemStatus::Upcoming, $item->status);
    }

    public function test_save_updates_an_existing_item_in_place(): void
    {
        MediaItemRecord::factory()->create(['slug' => 'the-long-game', 'type' => 'podcast', 'status' => 'upcoming', 'position' => 1]);

        $repository = new EloquentMediaItemRepository;

        $repository->save(new MediaItem(
            slug: Slug::fromString('the-long-game'),
            type: MediaType::Podcast,
            title: 'The Long Game: Economic Diplomacy in Practice',
            description: 'Updated description.',
            date: Carbon::parse('2026-07-01'),
            speaker: 'Naledi Okonjo-Reyes, Partner, Government & Political Affairs',
            status: MediaItemStatus::Published,
            position: 2,
        ));

        $this->assertCount(1, MediaItemRecord::all());
        $item = $repository->findBySlug(Slug::fromString('the-long-game'));
        $this->assertSame(MediaItemStatus::Published, $item->status);
        $this->assertSame(2, $item->position);
    }

    public function test_save_with_an_original_slug_renames_the_record(): void
    {
        MediaItemRecord::factory()->create(['slug' => 'old-slug', 'type' => 'livestream', 'position' => 1]);

        $repository = new EloquentMediaItemRepository;

        $repository->save(
            new MediaItem(
                slug: Slug::fromString('new-slug'),
                type: MediaType::Livestream,
                title: 'Renamed Livestream',
                description: 'Renamed description.',
                date: null,
                speaker: null,
                status: MediaItemStatus::Archived,
                position: 1,
            ),
            originalSlug: Slug::fromString('old-slug'),
        );

        $this->assertCount(1, MediaItemRecord::all());
        $this->assertNull($repository->findBySlug(Slug::fromString('old-slug')));
        $this->assertNotNull($repository->findBySlug(Slug::fromString('new-slug')));
    }

    public function test_delete_removes_the_item(): void
    {
        MediaItemRecord::factory()->create(['slug' => 'to-delete']);

        $repository = new EloquentMediaItemRepository;
        $repository->delete(Slug::fromString('to-delete'));

        $this->assertNull($repository->findBySlug(Slug::fromString('to-delete')));
    }
}
