<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Application\Content\Queries\ListEngagementModels;
use Domain\Shared\Exceptions\DomainException;
use Illuminate\Contracts\View\View;

final class EngagementModelController extends Controller
{
    public function __construct(private readonly ListEngagementModels $engagementModels) {}

    public function index(): View
    {
        return view('engagement-models.index', [
            'engagementModels' => ($this->engagementModels)(),
        ]);
    }

    public function show(string $slug): View
    {
        try {
            $engagementModel = $this->engagementModels->bySlug($slug);
        } catch (DomainException) {
            abort(404);
        }

        if ($engagementModel === null) {
            abort(404);
        }

        return view('engagement-models.show', [
            'engagementModel' => $engagementModel,
        ]);
    }
}
