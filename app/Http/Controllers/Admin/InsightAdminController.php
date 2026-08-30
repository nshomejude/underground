<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use DateTimeImmutable;
use Domain\Insights\Entities\Insight;
use Domain\Insights\Repositories\InsightRepository;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Staff-facing CRUD over the think tank's published insights. There is no
 * public-facing equivalent — see App\Http\Controllers\InsightController for
 * the read-only marketing pages this content feeds.
 */
final class InsightAdminController extends Controller
{
    public function __construct(
        private readonly InsightRepository $insights,
    ) {}

    public function index(): View
    {
        return view('admin.insights.index', [
            'insights' => $this->insights->all(),
        ]);
    }

    public function create(): View
    {
        return view('admin.insights.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $this->insights->save(new Insight(
            slug: Slug::fromString($data['slug']),
            title: $data['title'],
            category: $data['category'],
            excerpt: $data['excerpt'],
            body: $data['body'],
            publishedAt: $this->parsePublishedAt($data['published_at'] ?? null),
        ));

        return redirect()->route('admin.insights.index')->with('status', 'Insight created.');
    }

    public function edit(string $insight): View
    {
        $found = $this->insights->findBySlug(Slug::fromString($insight));

        abort_if($found === null, 404);

        return view('admin.insights.edit', ['insight' => $found]);
    }

    public function update(Request $request, string $insight): RedirectResponse
    {
        $existing = $this->insights->findBySlug(Slug::fromString($insight));

        abort_if($existing === null, 404);

        $data = $this->validated($request, forSlug: $existing->slug);

        $this->insights->save(new Insight(
            slug: $existing->slug,
            title: $data['title'],
            category: $data['category'],
            excerpt: $data['excerpt'],
            body: $data['body'],
            publishedAt: $this->parsePublishedAt($data['published_at'] ?? null),
        ));

        return redirect()->route('admin.insights.index')->with('status', 'Insight updated.');
    }

    public function destroy(string $insight): RedirectResponse
    {
        $existing = $this->insights->findBySlug(Slug::fromString($insight));

        abort_if($existing === null, 404);

        $this->insights->delete($existing->slug);

        return redirect()->route('admin.insights.index')->with('status', 'Insight deleted.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?Slug $forSlug = null): array
    {
        return $request->validate([
            'slug' => $forSlug === null
                ? ['required', 'string', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('insights', 'slug')]
                : ['sometimes', 'string'],
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'excerpt' => ['required', 'string', 'max:2000'],
            'body' => ['required', 'string'],
            'published_at' => ['nullable', 'date'],
        ], [
            'slug.regex' => 'The slug may only contain lowercase letters, numbers, and hyphens.',
        ]);
    }

    private function parsePublishedAt(?string $value): ?DateTimeImmutable
    {
        return $value === null || $value === '' ? null : new DateTimeImmutable($value);
    }
}
