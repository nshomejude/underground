<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EngagementModelRequest;
use Domain\Content\Entities\EngagementModel;
use Domain\Content\Repositories\EngagementModelRepository;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Admin CRUD for EngagementModel — the "how a client can retain the firm"
 * rows. See Domain\Content\Entities\EngagementModel.
 */
final class EngagementModelAdminController extends Controller
{
    public function __construct(private readonly EngagementModelRepository $engagementModels) {}

    public function index(): View
    {
        return view('admin.engagement-models.index', ['engagementModels' => $this->engagementModels->all()]);
    }

    public function create(): View
    {
        return view('admin.engagement-models.create');
    }

    public function store(EngagementModelRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $this->engagementModels->save(new EngagementModel(
            slug: Slug::fromString($data['slug']),
            name: $data['name'],
            summary: $data['summary'],
            icon: $data['icon'],
            position: (int) $data['position'],
        ));

        return redirect()->route('admin.engagement-models.index')->with('status', 'Engagement model created.');
    }

    public function edit(string $engagement_model): View
    {
        $found = $this->engagementModels->findBySlug(Slug::fromString($engagement_model));

        if ($found === null) {
            throw new NotFoundHttpException(sprintf('"%s" is not a known engagement model.', $engagement_model));
        }

        return view('admin.engagement-models.edit', ['engagementModel' => $found]);
    }

    public function update(string $engagement_model, EngagementModelRequest $request): RedirectResponse
    {
        $original = $this->engagementModels->findBySlug(Slug::fromString($engagement_model));

        if ($original === null) {
            throw new NotFoundHttpException(sprintf('"%s" is not a known engagement model.', $engagement_model));
        }

        $data = $request->validated();

        $this->engagementModels->save(
            new EngagementModel(
                slug: Slug::fromString($data['slug']),
                name: $data['name'],
                summary: $data['summary'],
                icon: $data['icon'],
                position: (int) $data['position'],
            ),
            originalSlug: $original->slug,
        );

        return redirect()->route('admin.engagement-models.index')->with('status', 'Engagement model updated.');
    }

    public function destroy(string $engagement_model): RedirectResponse
    {
        $this->engagementModels->delete(Slug::fromString($engagement_model));

        return redirect()->route('admin.engagement-models.index')->with('status', 'Engagement model removed.');
    }
}
