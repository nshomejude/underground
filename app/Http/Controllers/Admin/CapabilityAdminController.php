<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Domain\Content\Entities\Capability;
use Domain\Content\Repositories\CapabilityRepository;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Staff-facing CRUD over the disciplines Underground sells. There is no
 * public-facing equivalent — see App\Http\Controllers\CapabilityController
 * for the read-only marketing page this content feeds.
 */
final class CapabilityAdminController extends Controller
{
    /**
     * Mirrors the whitelist in resources/views/components/icon.blade.php —
     * only icons already wired sitewide may be assigned to a capability.
     */
    private const ICONS = [
        'globe', 'handshake', 'flag', 'landmark', 'shield-check', 'radar',
        'coins', 'megaphone', 'ship-wheel', 'library', 'target', 'gem',
        'building-2', 'newspaper', 'briefcase', 'scan-line', 'flashlight',
    ];

    public function __construct(
        private readonly CapabilityRepository $capabilities,
    ) {}

    public function index(): View
    {
        return view('admin.capabilities.index', [
            'capabilities' => $this->capabilities->all(),
        ]);
    }

    public function create(): View
    {
        return view('admin.capabilities.create', ['icons' => self::ICONS]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $this->capabilities->save(new Capability(
            slug: Slug::fromString($data['slug']),
            title: $data['title'],
            summary: $data['summary'],
            icon: $data['icon'],
            position: (int) $data['position'],
            isFeatured: $request->boolean('is_featured'),
        ));

        return redirect()->route('admin.capabilities.index')->with('status', 'Capability created.');
    }

    public function edit(string $capability): View
    {
        $found = $this->capabilities->findBySlug(Slug::fromString($capability));

        abort_if($found === null, 404);

        return view('admin.capabilities.edit', ['capability' => $found, 'icons' => self::ICONS]);
    }

    public function update(Request $request, string $capability): RedirectResponse
    {
        $existing = $this->capabilities->findBySlug(Slug::fromString($capability));

        abort_if($existing === null, 404);

        $data = $this->validated($request, forSlug: $existing->slug);

        $this->capabilities->save(new Capability(
            slug: $existing->slug,
            title: $data['title'],
            summary: $data['summary'],
            icon: $data['icon'],
            position: (int) $data['position'],
            isFeatured: $request->boolean('is_featured'),
        ));

        return redirect()->route('admin.capabilities.index')->with('status', 'Capability updated.');
    }

    public function destroy(string $capability): RedirectResponse
    {
        $existing = $this->capabilities->findBySlug(Slug::fromString($capability));

        abort_if($existing === null, 404);

        $this->capabilities->delete($existing->slug);

        return redirect()->route('admin.capabilities.index')->with('status', 'Capability deleted.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?Slug $forSlug = null): array
    {
        return $request->validate([
            'slug' => $forSlug === null
                ? ['required', 'string', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('capabilities', 'slug')]
                : ['sometimes', 'string'],
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['required', 'string', 'max:2000'],
            'icon' => ['required', 'string', Rule::in(self::ICONS)],
            'position' => ['required', 'integer', 'min:0'],
            'is_featured' => ['sometimes', 'boolean'],
        ], [
            'slug.regex' => 'The slug may only contain lowercase letters, numbers, and hyphens.',
        ]);
    }
}
