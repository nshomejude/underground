<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PartnerCategoryRequest;
use Domain\Content\Entities\PartnerCategory;
use Domain\Content\Repositories\PartnerCategoryRepository;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Admin CRUD for PartnerCategory — the /partners category tiles. See
 * Domain\Content\Entities\PartnerCategory.
 */
final class PartnerCategoryAdminController extends Controller
{
    public function __construct(private readonly PartnerCategoryRepository $categories) {}

    public function index(): View
    {
        return view('admin.partners.index', ['categories' => $this->categories->all()]);
    }

    public function create(): View
    {
        return view('admin.partners.create');
    }

    public function store(PartnerCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $this->categories->save(new PartnerCategory(
            slug: Slug::fromString($data['slug']),
            icon: $data['icon'],
            title: $data['title'],
            body: $data['body'],
            position: (int) $data['position'],
        ));

        return redirect()->route('admin.partners.index')->with('status', 'Partner category created.');
    }

    public function edit(string $partner_category): View
    {
        $found = $this->categories->findBySlug(Slug::fromString($partner_category));

        if ($found === null) {
            throw new NotFoundHttpException(sprintf('"%s" is not a known partner category.', $partner_category));
        }

        return view('admin.partners.edit', ['category' => $found]);
    }

    public function update(string $partner_category, PartnerCategoryRequest $request): RedirectResponse
    {
        $original = $this->categories->findBySlug(Slug::fromString($partner_category));

        if ($original === null) {
            throw new NotFoundHttpException(sprintf('"%s" is not a known partner category.', $partner_category));
        }

        $data = $request->validated();

        $this->categories->save(
            new PartnerCategory(
                slug: Slug::fromString($data['slug']),
                icon: $data['icon'],
                title: $data['title'],
                body: $data['body'],
                position: (int) $data['position'],
            ),
            originalSlug: $original->slug,
        );

        return redirect()->route('admin.partners.index')->with('status', 'Partner category updated.');
    }

    public function destroy(string $partner_category): RedirectResponse
    {
        $this->categories->delete(Slug::fromString($partner_category));

        return redirect()->route('admin.partners.index')->with('status', 'Partner category removed.');
    }
}
