<?php

declare(strict_types=1);

namespace Tests\Feature\Navigation;

use App\Support\OptionalRoute;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class NavLinksTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Pulls the <nav aria-label="Primary"> markup out of a response body so
     * assertions below only ever look at the primary desktop nav, not the
     * utility bar (language toggle intentionally uses href="#", see
     * site-header.blade.php).
     */
    private static function primaryNavHtml(string $body): string
    {
        $labelAt = strpos($body, 'aria-label="Primary"');
        self::assertNotFalse($labelAt, 'Expected an aria-label="Primary" nav in the response.');

        $tagStart = strrpos(substr($body, 0, $labelAt), '<nav');
        self::assertNotFalse($tagStart);

        $end = strpos($body, '</nav>', $labelAt);
        self::assertNotFalse($end);

        return substr($body, $tagStart, $end - $tagStart);
    }

    private static function footerHtml(string $body): string
    {
        $tagStart = strpos($body, '<footer');
        self::assertNotFalse($tagStart, 'Expected a <footer> element in the response.');

        $end = strpos($body, '</footer>', $tagStart);
        self::assertNotFalse($end);

        return substr($body, $tagStart, $end - $tagStart);
    }

    public function test_homepage_primary_nav_has_no_dead_hash_links(): void
    {
        $this->seed();

        $response = $this->get('/');
        $response->assertOk();

        $nav = self::primaryNavHtml($response->getContent());

        self::assertStringNotContainsString('href="#"', $nav);
    }

    public function test_homepage_footer_has_no_dead_hash_links(): void
    {
        $this->seed();

        $response = $this->get('/');
        $response->assertOk();

        $footer = self::footerHtml($response->getContent());

        self::assertStringNotContainsString('href="#"', $footer);
    }

    public function test_homepage_footer_is_wired_to_the_current_information_architecture(): void
    {
        $this->seed();

        $response = $this->get('/');
        $response->assertOk();

        $footer = self::footerHtml($response->getContent());

        self::assertStringContainsString(route('about'), $footer);
        self::assertStringContainsString(route('team'), $footer);
        self::assertStringContainsString(route('careers'), $footer);
        self::assertStringContainsString(route('partners'), $footer);
        self::assertStringContainsString(route('contact'), $footer);
        self::assertStringContainsString(route('capabilities.index'), $footer);
        self::assertStringContainsString(route('sectors.index'), $footer);
        self::assertStringContainsString(route('global-reach'), $footer);
        self::assertStringContainsString(route('team.case-studies'), $footer);
        self::assertStringContainsString(route('projects'), $footer);
        self::assertStringContainsString(route('reports-hub.index'), $footer);
        self::assertStringContainsString(route('flex-media-group'), $footer);
        self::assertStringContainsString(route('membership.index'), $footer);
        self::assertStringContainsString(route('events'), $footer);
        self::assertStringContainsString(route('consultations.create'), $footer);
    }

    public function test_homepage_primary_nav_is_wired_to_the_current_information_architecture(): void
    {
        $this->seed();

        $response = $this->get('/');
        $response->assertOk();

        $nav = self::primaryNavHtml($response->getContent());

        // The current IA (About, Capabilities, Global Reach, Reports Hub,
        // Team, Flex Media Group, Membership) per the Navigation module —
        // Expertise/Sectors, Insights, Careers and Contact were deliberately
        // dropped from the primary menu (Careers/Contact moved to the
        // footer only; Insights was replaced by Reports Hub).
        self::assertStringContainsString(route('about'), $nav);
        self::assertStringContainsString(route('capabilities.index'), $nav);
        self::assertStringContainsString(route('global-reach'), $nav);
        self::assertStringContainsString(route('team'), $nav);
        self::assertStringContainsString(route('membership.index'), $nav);

        // Reports Hub, Flex Media Group, and Global Reach's own sub-links
        // are owned by workstreams built in parallel and may not have
        // merged into this checkout yet — OptionalRoute (see
        // app/Support/OptionalRoute.php) resolves them to a guessed URL
        // rather than a dead '#' link until the real routes land.
        self::assertStringContainsString(
            OptionalRoute::url('reports-hub.index', [], '/reports-hub'),
            $nav,
        );
        self::assertStringContainsString(
            OptionalRoute::url('flex-media-group', [], '/flex-media-group'),
            $nav,
        );

        self::assertStringNotContainsString(route('careers'), $nav);
        self::assertStringNotContainsString(route('contact'), $nav);
    }
}
