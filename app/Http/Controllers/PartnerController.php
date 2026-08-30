<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

/**
 * The categories of organisations Underground works alongside. Types,
 * never names — the firm's partner relationships are as discreet as
 * its client mandates.
 */
final class PartnerController extends Controller
{
    public function index(): View
    {
        return view('partners.index', [
            'categories' => [
                [
                    'icon' => 'briefcase',
                    'title' => 'Advisory Firms',
                    'body' => 'Strategy, legal, and communications firms we bring in to extend a mandate\'s bench without ever widening who knows about it.',
                ],
                [
                    'icon' => 'coins',
                    'title' => 'Financial Institutions',
                    'body' => 'Banks, sovereign wealth vehicles, and institutional allocators we work alongside when a mandate turns on the movement of capital.',
                ],
                [
                    'icon' => 'globe',
                    'title' => 'Multilateral Organizations',
                    'body' => 'International bodies and development institutions whose mandates intersect with our clients\' — engaged through established, credentialed channels.',
                ],
                [
                    'icon' => 'radar',
                    'title' => 'Technology Partners',
                    'body' => 'Secure-communications, data, and analysis vendors vetted to the same standard we hold our own people to before they touch a mandate.',
                ],
            ],
        ]);
    }
}
