<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MediaItemRequest;
use Domain\Content\Entities\MediaItem;
use Domain\Content\Repositories\MediaItemRepository;
use Domain\Content\ValueObjects\MediaItemStatus;
use Domain\Content\ValueObjects\MediaType;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Admin CRUD for MediaItem — the /flex-media-group livestream, podcast,
 * and Underground Magazine listing. See Domain\Content\Entities\MediaItem.
 */
final class MediaItemAdminController extends Controller
{
    public function __construct(private readonly MediaItemRepository $mediaItems) {}

    public function index(): View
    {
        return view('admin.media-items.index', ['mediaItems' => $this->mediaItems->all()]);
    }

    public function create(): View
    {
        return view('admin.media-items.create');
    }

    public function store(MediaItemRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $this->mediaItems->save(new MediaItem(
            slug: Slug::fromString($data['slug']),
            type: MediaType::from($data['type']),
            title: $data['title'],
            description: $data['description'],
            date: isset($data['date']) ? Carbon::parse($data['date']) : null,
            speaker: $data['speaker'] ?? null,
            status: MediaItemStatus::from($data['status']),
            position: (int) $data['position'],
        ));

        return redirect()->route('admin.media-items.index')->with('status', 'Media item created.');
    }

    public function edit(string $media_item): View
    {
        $found = $this->mediaItems->findBySlug(Slug::fromString($media_item));

        if ($found === null) {
            throw new NotFoundHttpException(sprintf('"%s" is not a known media item.', $media_item));
        }

        return view('admin.media-items.edit', ['mediaItem' => $found]);
    }

    public function update(string $media_item, MediaItemRequest $request): RedirectResponse
    {
        $original = $this->mediaItems->findBySlug(Slug::fromString($media_item));

        if ($original === null) {
            throw new NotFoundHttpException(sprintf('"%s" is not a known media item.', $media_item));
        }

        $data = $request->validated();

        $this->mediaItems->save(
            new MediaItem(
                slug: Slug::fromString($data['slug']),
                type: MediaType::from($data['type']),
                title: $data['title'],
                description: $data['description'],
                date: isset($data['date']) ? Carbon::parse($data['date']) : null,
                speaker: $data['speaker'] ?? null,
                status: MediaItemStatus::from($data['status']),
                position: (int) $data['position'],
            ),
            originalSlug: $original->slug,
        );

        return redirect()->route('admin.media-items.index')->with('status', 'Media item updated.');
    }

    public function destroy(string $media_item): RedirectResponse
    {
        $this->mediaItems->delete(Slug::fromString($media_item));

        return redirect()->route('admin.media-items.index')->with('status', 'Media item removed.');
    }
}
