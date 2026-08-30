<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MetricRequest;
use Domain\Content\Entities\Metric;
use Domain\Content\Repositories\MetricRepository;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Admin CRUD for Metric — the credibility-bar proof points. See
 * Domain\Content\Entities\Metric.
 */
final class MetricAdminController extends Controller
{
    public function __construct(private readonly MetricRepository $metrics) {}

    public function index(): View
    {
        return view('admin.metrics.index', ['metrics' => $this->metrics->all()]);
    }

    public function create(): View
    {
        return view('admin.metrics.create');
    }

    public function store(MetricRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $this->metrics->save(new Metric(
            slug: Slug::fromString($data['slug']),
            value: $data['value'],
            label: $data['label'],
            icon: $data['icon'],
            position: (int) $data['position'],
        ));

        return redirect()->route('admin.metrics.index')->with('status', 'Metric created.');
    }

    public function edit(string $metric): View
    {
        $found = $this->metrics->findBySlug(Slug::fromString($metric));

        if ($found === null) {
            throw new NotFoundHttpException(sprintf('"%s" is not a known metric.', $metric));
        }

        return view('admin.metrics.edit', ['metric' => $found]);
    }

    public function update(string $metric, MetricRequest $request): RedirectResponse
    {
        $original = $this->metrics->findBySlug(Slug::fromString($metric));

        if ($original === null) {
            throw new NotFoundHttpException(sprintf('"%s" is not a known metric.', $metric));
        }

        $data = $request->validated();

        $this->metrics->save(
            new Metric(
                slug: Slug::fromString($data['slug']),
                value: $data['value'],
                label: $data['label'],
                icon: $data['icon'],
                position: (int) $data['position'],
            ),
            originalSlug: $original->slug,
        );

        return redirect()->route('admin.metrics.index')->with('status', 'Metric updated.');
    }

    public function destroy(string $metric): RedirectResponse
    {
        $this->metrics->delete(Slug::fromString($metric));

        return redirect()->route('admin.metrics.index')->with('status', 'Metric removed.');
    }
}
