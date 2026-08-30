<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Vite;
use Throwable;

/**
 * Leadership bios. The founder gets a real portrait (reused from the
 * landing page); every other principal gets an initials avatar so we
 * never have to fabricate a stock photo of a real-looking person.
 */
final class TeamController extends Controller
{
    public function index(): View
    {
        return view('team.index', [
            'founderPortraitSrc' => $this->founderPortraitSrc(),
            'leaders' => [
                [
                    'name' => 'Adrian Voss',
                    'title' => 'Founder & Managing Partner',
                    'background' => 'Two decades brokering quiet agreements between sovereigns, capital, and the institutions caught between them.',
                    'portrait' => true,
                ],
                [
                    'name' => 'Naledi Okonjo-Reyes',
                    'title' => 'Partner, Government & Political Affairs',
                    'background' => 'Former senior ministerial adviser who now sits on the other side of the table for a smaller number of principals.',
                    'icon' => 'landmark',
                ],
                [
                    'name' => 'Marcus Thane',
                    'title' => 'Partner, Strategic Intelligence & Analysis',
                    'background' => 'Built and ran research desks for two multinational institutions before joining the firm at its founding.',
                    'icon' => 'radar',
                ],
                [
                    'name' => 'Yumi Castellanos',
                    'title' => 'Partner, Investment & Capital Strategy',
                    'background' => 'Structured cross-border capital for sovereign and institutional allocators across three continents.',
                    'icon' => 'coins',
                ],
                [
                    'name' => 'Elias Farrow',
                    'title' => 'Partner, Crisis & Special Situations',
                    'background' => 'Called in when a mandate has already gone public — his job is to make sure it does not stay that way.',
                    'icon' => 'shield-check',
                ],
                [
                    'name' => 'Priya Anand-Whitfield',
                    'title' => 'Partner, Media & Narrative Management',
                    'background' => 'Two decades shaping how institutions are understood by the audiences that decide their fate.',
                    'icon' => 'megaphone',
                ],
            ],
        ]);
    }

    /**
     * Mirrors LandingPageController::founderPortraitSrc — resolved through
     * Vite so the asset is fingerprinted in production, falling back to a
     * plain public path when no build manifest exists (e.g. tests).
     */
    private function founderPortraitSrc(): string
    {
        try {
            return app(Vite::class)->asset('resources/images/founder-portrait.jpg');
        } catch (Throwable) {
            return asset('images/founder-portrait.jpg');
        }
    }
}
