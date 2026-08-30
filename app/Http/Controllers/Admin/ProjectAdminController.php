<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProjectRequest;
use Domain\Content\Entities\Project;
use Domain\Content\Repositories\ProjectRepository;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Admin CRUD for Project — the /projects initiative cards. See
 * Domain\Content\Entities\Project.
 */
final class ProjectAdminController extends Controller
{
    public function __construct(private readonly ProjectRepository $projects) {}

    public function index(): View
    {
        return view('admin.projects.index', ['projects' => $this->projects->all()]);
    }

    public function create(): View
    {
        return view('admin.projects.create');
    }

    public function store(ProjectRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $this->projects->save(new Project(
            slug: Slug::fromString($data['slug']),
            icon: $data['icon'],
            title: $data['title'],
            sector: $data['sector'],
            body: $data['body'],
            position: (int) $data['position'],
        ));

        return redirect()->route('admin.projects.index')->with('status', 'Project created.');
    }

    public function edit(string $project): View
    {
        $found = $this->projects->findBySlug(Slug::fromString($project));

        if ($found === null) {
            throw new NotFoundHttpException(sprintf('"%s" is not a known project.', $project));
        }

        return view('admin.projects.edit', ['project' => $found]);
    }

    public function update(string $project, ProjectRequest $request): RedirectResponse
    {
        $original = $this->projects->findBySlug(Slug::fromString($project));

        if ($original === null) {
            throw new NotFoundHttpException(sprintf('"%s" is not a known project.', $project));
        }

        $data = $request->validated();

        $this->projects->save(
            new Project(
                slug: Slug::fromString($data['slug']),
                icon: $data['icon'],
                title: $data['title'],
                sector: $data['sector'],
                body: $data['body'],
                position: (int) $data['position'],
            ),
            originalSlug: $original->slug,
        );

        return redirect()->route('admin.projects.index')->with('status', 'Project updated.');
    }

    public function destroy(string $project): RedirectResponse
    {
        $this->projects->delete(Slug::fromString($project));

        return redirect()->route('admin.projects.index')->with('status', 'Project removed.');
    }
}
