<?php

declare(strict_types=1);

namespace App\Http\Controllers;

/**
 * Underground's office network and departmental mailboxes, shared by the
 * Contact and Global Reach pages.
 */
final class OfficeDirectory
{
    /**
     * @return list<array{id: string, city: string, region: string, address: ?string, email: ?string, phone: ?string, note: string, lat: float, lng: float, blurb: string}>
     */
    public static function all(): array
    {
        return [
            [
                'id' => 'dc',
                'city' => 'Washington, D.C.',
                'region' => 'United States',
                'address' => "200 Massachusetts Ave NW\nWashington, DC 20001, USA",
                'email' => 'info@un-der.com',
                'phone' => '+1-571-508-9170',
                'note' => 'Headquarters',
                'lat' => 38.9072,
                'lng' => -77.0369,
                'blurb' => 'Our headquarters: strategy, capital partnerships and government relations.',
            ],
            [
                'id' => 'dla',
                'city' => 'Douala',
                'region' => 'Cameroon',
                'address' => null,
                'email' => null,
                'phone' => '+237 68000 1010',
                'note' => 'Central Africa office',
                'lat' => 4.0511,
                'lng' => 9.7679,
                'blurb' => 'Port city access to Central Africa and the Gulf of Guinea.',
            ],
            [
                'id' => 'abj',
                'city' => 'Abidjan',
                'region' => 'Côte d’Ivoire',
                'address' => null,
                'email' => null,
                'phone' => null,
                'note' => 'West Africa office',
                'lat' => 5.3600,
                'lng' => -4.0083,
                'blurb' => 'Anchor for francophone West Africa and regional logistics.',
            ],
            [
                'id' => 'los',
                'city' => 'Lagos',
                'region' => 'Nigeria',
                'address' => null,
                'email' => null,
                'phone' => null,
                'note' => 'West Africa office',
                'lat' => 6.5244,
                'lng' => 3.3792,
                'blurb' => 'Commercial hub for Nigeria, energy and technology matters.',
            ],
            [
                'id' => 'par',
                'city' => 'Paris',
                'region' => 'France',
                'address' => null,
                'email' => null,
                'phone' => null,
                'note' => 'Europe office',
                'lat' => 48.8566,
                'lng' => 2.3522,
                'blurb' => 'Gateway to European institutions, finance and francophone partners.',
            ],
        ];
    }

    /**
     * @return array<string, string> label => mailbox
     */
    public static function departments(): array
    {
        return [
            'Partnerships' => 'partnerships@un-der.com',
            'Proposals' => 'proposals@un-der.com',
            'Projects' => 'projects@un-der.com',
            'Media' => 'media@un-der.com',
            'Policy' => 'policy@un-der.com',
            'Business' => 'business@un-der.com',
        ];
    }
}
