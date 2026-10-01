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
     * @return list<array{city: string, region: string, address: ?string, email: ?string, phone: ?string, note: string}>
     */
    public static function all(): array
    {
        return [
            [
                'city' => 'Washington, D.C.',
                'region' => 'United States',
                'address' => "200 Massachusetts Ave NW\nWashington, DC 20001, USA",
                'email' => 'info@un-der.com',
                'phone' => '+1-571-508-9170',
                'note' => 'Headquarters',
            ],
            [
                'city' => 'Douala',
                'region' => 'Cameroon',
                'address' => null,
                'email' => null,
                'phone' => '+237 68000 1010',
                'note' => 'Central Africa office',
            ],
            [
                'city' => 'Abidjan',
                'region' => 'Côte d’Ivoire',
                'address' => null,
                'email' => null,
                'phone' => null,
                'note' => 'West Africa office',
            ],
            [
                'city' => 'Lagos',
                'region' => 'Nigeria',
                'address' => null,
                'email' => null,
                'phone' => null,
                'note' => 'West Africa office',
            ],
            [
                'city' => 'Paris',
                'region' => 'France',
                'address' => null,
                'email' => null,
                'phone' => null,
                'note' => 'Europe office',
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
