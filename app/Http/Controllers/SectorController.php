<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Application\Content\Queries\ListSectors;
use Domain\Shared\Exceptions\DomainException;
use Illuminate\Contracts\View\View;

final class SectorController extends Controller
{
    public function __construct(private readonly ListSectors $sectors) {}

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

        return view('sectors.show', [
            'sector' => $sector,
        ]);
    }
}
