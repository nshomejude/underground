<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Application\Content\Queries\ListCapabilities;
use Domain\Shared\Exceptions\DomainException;
use Illuminate\Contracts\View\View;

final class CapabilityController extends Controller
{
    public function __construct(private readonly ListCapabilities $capabilities) {}

    public function show(string $slug): View
    {
        try {
            $capability = $this->capabilities->bySlug($slug);
        } catch (DomainException) {
            abort(404);
        }

        if ($capability === null) {
            abort(404);
        }

        return view('capabilities.show', [
            'capability' => $capability,
        ]);
    }
}
