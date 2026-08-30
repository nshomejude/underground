<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SectorRequest;
use Domain\Content\Entities\Sector;
use Domain\Content\Repositories\SectorRepository;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Admin CRUD for Sector — the "sectors we serve" tiles. See
 * Domain\Content\Entities\Sector.
 */
final class SectorAdminController extends Controller
{
    public function __construct(private readonly SectorRepository $sectors) {}

    public function index(): View
    {
        return view('admin.sectors.index', ['sectors' => $this->sectors->all()]);
    }

    public function create(): View
    {
        return view('admin.sectors.create');
    }

    public function store(SectorRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $this->sectors->save(new Sector(
            slug: Slug::fromString($data['slug']),
            name: $data['name'],
            summary: $data['summary'],
            motif: $data['motif'],
            position: (int) $data['position'],
        ));

        return redirect()->route('admin.sectors.index')->with('status', 'Sector created.');
    }

    public function edit(string $sector): View
    {
        $found = $this->sectors->findBySlug(Slug::fromString($sector));

        if ($found === null) {
            throw new NotFoundHttpException(sprintf('"%s" is not a known sector.', $sector));
        }

        return view('admin.sectors.edit', ['sector' => $found]);
    }

    public function update(string $sector, SectorRequest $request): RedirectResponse
    {
        $original = $this->sectors->findBySlug(Slug::fromString($sector));

        if ($original === null) {
            throw new NotFoundHttpException(sprintf('"%s" is not a known sector.', $sector));
        }

        $data = $request->validated();

        $this->sectors->save(
            new Sector(
                slug: Slug::fromString($data['slug']),
                name: $data['name'],
                summary: $data['summary'],
                motif: $data['motif'],
                position: (int) $data['position'],
            ),
            originalSlug: $original->slug,
        );

        return redirect()->route('admin.sectors.index')->with('status', 'Sector updated.');
    }

    public function destroy(string $sector): RedirectResponse
    {
        $this->sectors->delete(Slug::fromString($sector));

        return redirect()->route('admin.sectors.index')->with('status', 'Sector removed.');
    }
}
