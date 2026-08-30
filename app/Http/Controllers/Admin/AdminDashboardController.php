<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Landing page for staff at /admin: links out to every admin section
 * contributed across the review-queue and content-admin modules. Owns no
 * data of its own — purely navigational.
 */
final class AdminDashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.index');
    }
}
