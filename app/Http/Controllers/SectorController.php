<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Application\Content\Queries\ListCapabilities;
use Application\Content\Queries\ListSectors;
use Domain\Shared\Exceptions\DomainException;
use Illuminate\Contracts\View\View;

final class SectorController extends Controller
{
    public function __construct(
        private readonly ListSectors $sectors,
        private readonly ListCapabilities $capabilities,
    ) {}

    public function index(): View
    {
        return view('sectors.index', [
            'sectors' => ($this->sectors)(),
        ]);
    }

    public function show(string $slug): View
    {
        try {
            $sector = $this->sectors->bySlug($slug);
        } catch (DomainException) {
            abort(404);
        }

        if ($sector === null) {
            abort(404);
        }

        $content = config('page_content.sectors.'.$sector->slug->value, []);
        $slugs = $content['capabilities'] ?? [];

        return view('sectors.show', [
            'sector' => $sector,
            'content' => $content,
            'relatedCapabilities' => array_values(array_filter(
                ($this->capabilities)(),
                static fn ($capability): bool => in_array($capability->slug->value, $slugs, true),
            )),
            'otherSectors' => array_values(array_filter(
                ($this->sectors)(),
                static fn ($other): bool => $other->slug->value !== $sector->slug->value,
            )),
        ]);
    }
}
