<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GalleryPhotoRequest;
use Domain\Content\Entities\GalleryPhoto;
use Domain\Content\Repositories\GalleryPhotoRepository;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Admin CRUD for GalleryPhoto — the /team/gallery photo grid. See
 * Domain\Content\Entities\GalleryPhoto.
 */
final class GalleryPhotoAdminController extends Controller
{
    public function __construct(private readonly GalleryPhotoRepository $photos) {}

    public function index(): View
    {
        return view('admin.gallery.index', ['photos' => $this->photos->all()]);
    }

    public function create(): View
    {
        return view('admin.gallery.create');
    }

    public function store(GalleryPhotoRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $this->photos->save(new GalleryPhoto(
            slug: Slug::fromString($data['slug']),
            caption: $data['caption'],
            context: ($data['context'] ?? null) ?: null,
            capturedOn: ($data['captured_on'] ?? null) ?: null,
            photoRightsNote: $data['photo_rights_note'],
            imageUrl: ($data['image_url'] ?? null) ?: null,
            position: (int) $data['position'],
        ));

        return redirect()->route('admin.gallery.index')->with('status', 'Photo created.');
    }

    public function edit(string $gallery_photo): View
    {
        $found = $this->photos->findBySlug(Slug::fromString($gallery_photo));

        if ($found === null) {
            throw new NotFoundHttpException(sprintf('"%s" is not a known photo.', $gallery_photo));
        }

        return view('admin.gallery.edit', ['photo' => $found]);
    }

    public function update(string $gallery_photo, GalleryPhotoRequest $request): RedirectResponse
    {
        $original = $this->photos->findBySlug(Slug::fromString($gallery_photo));

        if ($original === null) {
            throw new NotFoundHttpException(sprintf('"%s" is not a known photo.', $gallery_photo));
        }

        $data = $request->validated();

        $this->photos->save(
            new GalleryPhoto(
                slug: Slug::fromString($data['slug']),
                caption: $data['caption'],
                context: ($data['context'] ?? null) ?: null,
                capturedOn: ($data['captured_on'] ?? null) ?: null,
                photoRightsNote: $data['photo_rights_note'],
                imageUrl: ($data['image_url'] ?? null) ?: null,
                position: (int) $data['position'],
            ),
            originalSlug: $original->slug,
        );

        return redirect()->route('admin.gallery.index')->with('status', 'Photo updated.');
    }

    public function destroy(string $gallery_photo): RedirectResponse
    {
        $this->photos->delete(Slug::fromString($gallery_photo));

        return redirect()->route('admin.gallery.index')->with('status', 'Photo removed.');
    }
}
