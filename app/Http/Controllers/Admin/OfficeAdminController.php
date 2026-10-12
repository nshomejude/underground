<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OfficeRequest;
use Domain\Content\Entities\Office;
use Domain\Content\Repositories\OfficeRepository;
use Domain\Content\ValueObjects\OfficeStatus;
use Domain\Content\ValueObjects\RelationshipType;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Admin CRUD for Office — the alliance network of locations and regional
 * coverage shown on /global-reach, /global-alliance and /locations. See
 * Domain\Content\Entities\Office.
 */
final class OfficeAdminController extends Controller
{
    public function __construct(private readonly OfficeRepository $offices) {}

    public function index(): View
    {
        return view('admin.offices.index', ['offices' => $this->offices->all()]);
    }

    public function create(): View
    {
        return view('admin.offices.create', [
            'statuses' => OfficeStatus::cases(),
            'relationshipTypes' => RelationshipType::cases(),
        ]);
    }

    public function store(OfficeRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $this->offices->save(new Office(
            slug: Slug::fromString($data['slug']),
            name: $data['name'],
            region: $data['region'],
            address: $data['address'] ?? null,
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            blurb: $data['blurb'],
            lat: isset($data['lat']) && $data['lat'] !== '' ? (float) $data['lat'] : null,
            lng: isset($data['lng']) && $data['lng'] !== '' ? (float) $data['lng'] : null,
            countries: $this->parseCountries($data['countries_text'] ?? ''),
            status: OfficeStatus::from($data['status']),
            relationshipType: RelationshipType::from($data['relationship_type']),
            isHeadquarters: (bool) ($data['is_headquarters'] ?? false),
            position: (int) $data['position'],
        ));

        return redirect()->route('admin.offices.index')->with('status', 'Office created.');
    }

    public function edit(string $office): View
    {
        $found = $this->offices->findBySlug(Slug::fromString($office));

        if ($found === null) {
            throw new NotFoundHttpException(sprintf('"%s" is not a known office.', $office));
        }

        return view('admin.offices.edit', [
            'office' => $found,
            'statuses' => OfficeStatus::cases(),
            'relationshipTypes' => RelationshipType::cases(),
        ]);
    }

    public function update(string $office, OfficeRequest $request): RedirectResponse
    {
        $original = $this->offices->findBySlug(Slug::fromString($office));

        if ($original === null) {
            throw new NotFoundHttpException(sprintf('"%s" is not a known office.', $office));
        }

        $data = $request->validated();

        $this->offices->save(
            new Office(
                slug: Slug::fromString($data['slug']),
                name: $data['name'],
                region: $data['region'],
                address: $data['address'] ?? null,
                email: $data['email'] ?? null,
                phone: $data['phone'] ?? null,
                blurb: $data['blurb'],
                lat: isset($data['lat']) && $data['lat'] !== '' ? (float) $data['lat'] : null,
                lng: isset($data['lng']) && $data['lng'] !== '' ? (float) $data['lng'] : null,
                countries: $this->parseCountries($data['countries_text'] ?? ''),
                status: OfficeStatus::from($data['status']),
                relationshipType: RelationshipType::from($data['relationship_type']),
                isHeadquarters: (bool) ($data['is_headquarters'] ?? false),
                position: (int) $data['position'],
            ),
            originalSlug: $original->slug,
        );

        return redirect()->route('admin.offices.index')->with('status', 'Office updated.');
    }

    public function destroy(string $office): RedirectResponse
    {
        $this->offices->delete(Slug::fromString($office));

        return redirect()->route('admin.offices.index')->with('status', 'Office removed.');
    }

    /**
     * One country/area per line, trimmed, blank lines dropped — the same
     * newline-delimited textarea convention NarrativeAdminController uses
     * for its own list fields.
     *
     * @return list<string>
     */
    private function parseCountries(string $text): array
    {
        return array_values(array_filter(
            array_map('trim', explode("\n", str_replace("\r\n", "\n", $text))),
            fn (string $line): bool => $line !== '',
        ));
    }
}
