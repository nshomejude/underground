<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PillarRequest;
use Domain\Content\Entities\Pillar;
use Domain\Content\Repositories\PillarRepository;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Admin CRUD for Pillar — the four brand pillars. See
 * Domain\Content\Entities\Pillar.
 */
final class PillarAdminController extends Controller
{
    public function __construct(private readonly PillarRepository $pillars) {}

    public function index(): View
    {
        return view('admin.pillars.index', ['pillars' => $this->pillars->all()]);
    }

    public function create(): View
    {
        return view('admin.pillars.create');
    }

    public function store(PillarRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $this->pillars->save(new Pillar(
            slug: Slug::fromString($data['slug']),
            title: $data['title'],
            qualifier: $data['qualifier'],
            icon: $data['icon'],
            position: (int) $data['position'],
        ));

        return redirect()->route('admin.pillars.index')->with('status', 'Pillar created.');
    }

    public function edit(string $pillar): View
    {
        $found = $this->pillars->findBySlug(Slug::fromString($pillar));

        if ($found === null) {
            throw new NotFoundHttpException(sprintf('"%s" is not a known pillar.', $pillar));
        }

        return view('admin.pillars.edit', ['pillar' => $found]);
    }

    public function update(string $pillar, PillarRequest $request): RedirectResponse
    {
        $original = $this->pillars->findBySlug(Slug::fromString($pillar));

        if ($original === null) {
            throw new NotFoundHttpException(sprintf('"%s" is not a known pillar.', $pillar));
        }

        $data = $request->validated();

        $this->pillars->save(
            new Pillar(
                slug: Slug::fromString($data['slug']),
                title: $data['title'],
                qualifier: $data['qualifier'],
                icon: $data['icon'],
                position: (int) $data['position'],
            ),
            originalSlug: $original->slug,
        );

        return redirect()->route('admin.pillars.index')->with('status', 'Pillar updated.');
    }

    public function destroy(string $pillar): RedirectResponse
    {
        $this->pillars->delete(Slug::fromString($pillar));

        return redirect()->route('admin.pillars.index')->with('status', 'Pillar removed.');
    }
}
