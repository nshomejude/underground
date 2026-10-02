<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Application\Content\Queries\ListCapabilities;
use Application\Content\Queries\ListEngagementModels;
use Application\Content\Queries\ListSectors;
use Application\Insights\Queries\ListInsights;
use Illuminate\Http\Response;

/**
 * XML sitemap of every indexable public URL, generated from the same
 * queries the pages themselves use so it cannot drift from the site.
 */
final class SitemapController extends Controller
{
    public function __construct(
        private readonly ListCapabilities $capabilities,
        private readonly ListSectors $sectors,
        private readonly ListEngagementModels $engagementModels,
        private readonly ListInsights $insights,
    ) {}

    public function __invoke(): Response
    {
        $urls = [];

        foreach ([
            ['home', 1.0], ['about', 0.8], ['capabilities.index', 0.9], ['sectors.index', 0.9],
            ['engagement-models.index', 0.8], ['global-reach', 0.8], ['insights.index', 0.8],
            ['team', 0.7], ['partners', 0.6], ['collaboration', 0.6], ['portfolio', 0.7],
            ['projects', 0.6], ['events', 0.6], ['careers', 0.5], ['membership.index', 0.6],
            ['inquiries.create', 0.7], ['contact', 0.8], ['terms', 0.3], ['privacy', 0.3],
        ] as [$name, $priority]) {
            $urls[] = ['loc' => route($name), 'priority' => $priority, 'lastmod' => null];
        }

        foreach (($this->capabilities)() as $item) {
            $urls[] = ['loc' => route('capabilities.show', $item->slug->value), 'priority' => 0.8, 'lastmod' => null];
        }

        foreach (($this->sectors)() as $item) {
            $urls[] = ['loc' => route('sectors.show', $item->slug->value), 'priority' => 0.8, 'lastmod' => null];
        }

        foreach (($this->engagementModels)() as $item) {
            $urls[] = ['loc' => route('engagement-models.show', $item->slug->value), 'priority' => 0.7, 'lastmod' => null];
        }

        foreach (($this->insights)() as $item) {
            $urls[] = [
                'loc' => route('insights.show', $item->slug->value),
                'priority' => 0.7,
                'lastmod' => $item->publishedAt?->format('Y-m-d'),
            ];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls as $url) {
            $xml .= '  <url><loc>'.htmlspecialchars($url['loc'], ENT_XML1).'</loc>';

            if ($url['lastmod']) {
                $xml .= '<lastmod>'.$url['lastmod'].'</lastmod>';
            }

            $xml .= '<priority>'.number_format($url['priority'], 1).'</priority></url>'."\n";
        }

        $xml .= '</urlset>'."\n";

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
