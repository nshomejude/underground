<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Application\Insights\Queries\ListInsights;
use Domain\Shared\Exceptions\DomainException;
use Illuminate\Contracts\View\View;

final class InsightController extends Controller
{
    public function __construct(private readonly ListInsights $insights) {}

    public function index(): View
    {
        return view('insights.index', [
            'insights' => ($this->insights)(),
        ]);
    }

    public function show(string $slug): View
    {
        try {
            $insight = $this->insights->bySlug($slug);
        } catch (DomainException) {
            abort(404);
        }

        if ($insight === null) {
            abort(404);
        }

        return view('insights.show', [
            'insight' => $insight,
        ]);
    }
}
