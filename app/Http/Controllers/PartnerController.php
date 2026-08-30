<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Application\Content\Queries\ListPartnerCategories;
use Illuminate\Contracts\View\View;

/**
 * The categories of organisations Underground works alongside. Types,
 * never names — the firm's partner relationships are as discreet as
 * its client mandates. Content is admin-editable — see
 * Admin\PartnerCategoryAdminController.
 */
final class PartnerController extends Controller
{
    public function __construct(private readonly ListPartnerCategories $categories) {}

    public function index(): View
    {
        return view('partners.index', [
            'categories' => array_map(
                static fn ($category) => $category->toArray(),
                ($this->categories)(),
            ),
        ]);
    }
}
