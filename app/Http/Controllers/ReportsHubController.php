<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Application\Insights\Queries\ListInsights;
use Domain\Shared\Exceptions\DomainException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Reports Hub: the renamed, repositioned successor to Insights. Reads the
 * same underlying Insight aggregate (src/Domain/Insights) extended with
 * author/country/sector/access-level metadata -- see
 * Application\Insights\Queries\ListInsights for the filtering/sorting this
 * controller delegates to.
 */
final class ReportsHubController extends Controller
{
    private const array SORTS = ['newest', 'oldest', 'title'];

    public function __construct(private readonly ListInsights $reports) {}

    public function index(Request $request): View
    {
        $category = $this->stringQuery($request, 'category');
        $country = $this->stringQuery($request, 'country');
        $sector = $this->stringQuery($request, 'sector');
        $sort = $request->query('sort');
        $sort = in_array($sort, self::SORTS, true) ? $sort : 'newest';

        return view('reports-hub.index', [
            'reports' => $this->reports->filtered($category, $country, $sector, $sort),
            'categories' => $this->reports->categories(),
            'countries' => $this->reports->countries(),
            'sectors' => $this->reports->sectors(),
            'filters' => [
                'category' => $category,
                'country' => $country,
                'sector' => $sector,
                'sort' => $sort,
            ],
        ]);
    }

    public function strategicNotes(): View
    {
        return view('reports-hub.strategic-notes', [
            'reports' => $this->reports->strategicNotes(),
        ]);
    }

    public function show(string $slug): View
    {
        try {
            $report = $this->reports->bySlug($slug);
        } catch (DomainException) {
            abort(404);
        }

        if ($report === null) {
            abort(404);
        }

        return view('reports-hub.show', [
            'report' => $report,
        ]);
    }

    private function stringQuery(Request $request, string $key): ?string
    {
        $value = $request->query($key);

        return is_string($value) && $value !== '' ? $value : null;
    }
}
