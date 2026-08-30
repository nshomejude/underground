<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Application\Content\Queries\ListProjects;
use Illuminate\Contracts\View\View;

/**
 * Current, ongoing initiatives — distinct from Portfolio, which is
 * closed, past work. Nothing here names a client; these describe the
 * shape of the work in flight, not who it is for. Content is
 * admin-editable — see Admin\ProjectAdminController.
 */
final class ProjectController extends Controller
{
    public function __construct(private readonly ListProjects $projects) {}

    public function index(): View
    {
        return view('projects.index', [
            'projects' => array_map(
                static fn ($project) => $project->toArray(),
                ($this->projects)(),
            ),
        ]);
    }
}
