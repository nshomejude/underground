<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

/**
 * General contact information. The real intake mechanism is the
 * Confidential Inquiry form (Engagement context) — this page only
 * orients a visitor toward it, plus general offices for correspondence
 * that isn't a new mandate.
 */
final class ContactController extends Controller
{
    public function index(): View
    {
        return view('contact.index', [
            'generalEmail' => 'office@underground-network.example',
            'offices' => [
                [
                    'city' => 'London',
                    'region' => 'United Kingdom',
                    'address' => 'One Berkeley Square, Mayfair, London W1J 6BD',
                    'note' => 'European and government affairs desk',
                ],
                [
                    'city' => 'Geneva',
                    'region' => 'Switzerland',
                    'address' => 'Quai du Mont-Blanc 15, 1201 Genève',
                    'note' => 'Multilateral and diplomatic liaison',
                ],
                [
                    'city' => 'Singapore',
                    'region' => 'Singapore',
                    'address' => '1 Raffles Place, Tower One, Singapore 048616',
                    'note' => 'Asia-Pacific capital and infrastructure desk',
                ],
                [
                    'city' => 'Washington, D.C.',
                    'region' => 'United States',
                    'address' => '1717 Pennsylvania Avenue NW, Washington, DC 20006',
                    'note' => 'North American public affairs desk',
                ],
            ],
        ]);
    }
}
