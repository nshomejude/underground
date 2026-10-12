<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Domain\Content\ValueObjects\OfficeStatus;
use Domain\Content\ValueObjects\RelationshipType;
use Illuminate\Contracts\View\View;

/**
 * Explains Underground's alliance model on its own terms: what each
 * status and relationship type does and does not claim, and a direct
 * statement that partners are independent firms or principals — never
 * Underground employees or subsidiaries. See Domain\Content\Entities\Office
 * and its OfficeStatus / RelationshipType value objects for the single
 * source of truth this page describes.
 */
final class GlobalAllianceController extends Controller
{
    public function __invoke(): View
    {
        return view('global-alliance.index', [
            'statuses' => OfficeStatus::cases(),
            'relationshipTypes' => RelationshipType::cases(),
        ]);
    }
}
