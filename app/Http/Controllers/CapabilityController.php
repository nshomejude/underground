<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Application\Content\Queries\ListCapabilities;
use Application\Content\Queries\ListEngagementModels;
use Application\Content\Queries\ListSectors;
use Domain\Shared\Exceptions\DomainException;
use Illuminate\Contracts\View\View;

final class CapabilityController extends Controller
{
    public function __construct(
        private readonly ListCapabilities $capabilities,
        private readonly ListSectors $sectors,
        private readonly ListEngagementModels $engagementModels,
    ) {}

    public function index(): View
    {
        return view('capabilities.index', [
            'capabilities' => ($this->capabilities)(),
        ]);
    }

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

        $content = config('page_content.capabilities.'.$capability->slug->value, []);

        return view('capabilities.show', [
            'capability' => $capability,
            'content' => $content,
            'relatedSectors' => $this->pick(($this->sectors)(), $content['sectors'] ?? []),
            'relatedEngagementModels' => $this->pick(($this->engagementModels)(), $content['engagement'] ?? []),
            'otherCapabilities' => array_values(array_filter(
                ($this->capabilities)(),
                static fn ($other): bool => $other->slug->value !== $capability->slug->value,
            )),
        ]);
    }

    /**
     * @param  list<object>  $items
     * @param  list<string>  $slugs
     * @return list<object>
     */
    private function pick(array $items, array $slugs): array
    {
        return array_values(array_filter(
            $items,
            static fn ($item): bool => in_array($item->slug->value, $slugs, true),
        ));
    }
}
