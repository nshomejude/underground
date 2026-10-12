<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Application\Content\Queries\ListMediaItems;
use Domain\Content\ValueObjects\MediaType;
use Illuminate\Contracts\View\View;

/**
 * Flex Media Group: Underground's livestream, podcast, and Underground
 * Magazine output, shown on /flex-media-group as three sections. Content
 * is admin-editable — see Admin\MediaItemAdminController.
 */
final class FlexMediaGroupController extends Controller
{
    public function __construct(private readonly ListMediaItems $mediaItems) {}

    public function index(): View
    {
        $items = array_map(
            static fn ($item) => $item->toArray(),
            ($this->mediaItems)(),
        );

        $byType = static fn (MediaType $type) => array_values(array_filter(
            $items,
            static fn (array $item) => $item['type'] === $type->value,
        ));

        return view('flex-media-group.index', [
            'livestreams' => $byType(MediaType::Livestream),
            'podcasts' => $byType(MediaType::Podcast),
            'magazineIssues' => $byType(MediaType::Magazine),
        ]);
    }
}
