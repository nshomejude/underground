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
            problemStatement: $data['problem_statement'],
            audiences: $this->linesToArray($data['audiences']),
            method: $data['method'],
            deliverables: $this->linesToArray($data['deliverables']),
            confirmedLead: $this->blankToNull($data['confirmed_lead'] ?? null),
            authorizedReference: $this->blankToNull($data['authorized_reference'] ?? null),
            nextAction: $data['next_action'],
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
            problemStatement: $data['problem_statement'],
            audiences: $this->linesToArray($data['audiences']),
            method: $data['method'],
            deliverables: $this->linesToArray($data['deliverables']),
            confirmedLead: $this->blankToNull($data['confirmed_lead'] ?? null),
            authorizedReference: $this->blankToNull($data['authorized_reference'] ?? null),
            nextAction: $data['next_action'],
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
            'problem_statement' => ['required', 'string', 'max:2000'],
            'audiences' => ['required', 'string'],
            'method' => ['required', 'string', 'max:2000'],
            'deliverables' => ['required', 'string'],
            'confirmed_lead' => ['nullable', 'string', 'max:255'],
            'authorized_reference' => ['nullable', 'string', 'max:255'],
            'next_action' => ['required', 'string', 'max:255'],
        ], [
            'slug.regex' => 'The slug may only contain lowercase letters, numbers, and hyphens.',
        ]);
    }

    /**
     * Converts a textarea's newline-delimited lines into a clean list,
     * dropping blank lines — the same convention NarrativeAdminController
     * uses for its headline field.
     *
     * @return list<string>
     */
    private function linesToArray(string $text): array
    {
        return array_values(array_filter(
            array_map('trim', explode("\n", str_replace("\r\n", "\n", $text))),
            fn (string $line): bool => $line !== '',
        ));
    }

    /**
     * An empty form field arrives as '', not absent — treat it the same as
     * "not confirmed" so the detail page omits the section rather than
     * rendering an empty one.
     */
    private function blankToNull(?string $value): ?string
    {
        $trimmed = trim((string) $value);

        return $trimmed === '' ? null : $trimmed;
    }
}
