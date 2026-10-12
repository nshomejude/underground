<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Infrastructure\Persistence\Eloquent\Models\OfficeRecord;

/**
 * Seeds Underground's alliance network — offices and regional coverage —
 * shown on /global-reach, /global-alliance and /locations.
 *
 * Migrated from the previously hardcoded App\Http\Controllers\OfficeDirectory
 * array. Only Washington, D.C. is an ActiveOffice: Underground's one
 * directly-operated, Underground-staffed location. Every other entry
 * defaults to PartnerCoverage (the four pre-existing locations) or to
 * the status noted below for the newly added regional/continental
 * coverage, so the site never implies an owned branch or an employee
 * relationship it has not confirmed.
 */
final class OfficeSeeder extends Seeder
{
    public function run(): void
    {
        $offices = [
            // --- Confirmed locations (migrated from OfficeDirectory) -----
            // Slugs below match the old OfficeDirectory array's ids (dc,
            // dla, abj, los, par) on purpose: the globe-atlas component's
            // network-link pairs and label-placement map are keyed by
            // these exact short slugs.
            [
                'slug' => 'dc',
                'name' => 'Washington, D.C.',
                'region' => 'United States',
                'address' => "200 Massachusetts Ave NW\nWashington, DC 20001, USA",
                'email' => 'info@un-der.com',
                'phone' => '+1-571-508-9170',
                'blurb' => 'Our headquarters: strategy, capital partnerships and government relations.',
                'lat' => 38.9072,
                'lng' => -77.0369,
                'countries' => ['United States'],
                'status' => 'active_office',
                'relationship_type' => 'independent',
                'is_headquarters' => true,
            ],
            [
                'slug' => 'dla',
                'name' => 'Douala',
                'region' => 'Cameroon',
                'address' => null,
                'email' => null,
                'phone' => '+237 68000 1010',
                'blurb' => 'Port city access to Central Africa and the Gulf of Guinea, through an independent local partner.',
                'lat' => 4.0511,
                'lng' => 9.7679,
                'countries' => ['Cameroon'],
                // Pre-existing location: defaults to Partner Coverage per the
                // client brief — nothing in this codebase establishes a
                // staffed Underground branch here.
                'status' => 'partner_coverage',
                'relationship_type' => 'partnership',
                'is_headquarters' => false,
            ],
            [
                'slug' => 'abj',
                'name' => 'Abidjan',
                'region' => 'Côte d’Ivoire',
                'address' => null,
                'email' => null,
                'phone' => null,
                'blurb' => 'Anchor for francophone West Africa and regional logistics, through an independent local partner.',
                'lat' => 5.3600,
                'lng' => -4.0083,
                'countries' => ['Côte d’Ivoire'],
                'status' => 'partner_coverage',
                'relationship_type' => 'partnership',
                'is_headquarters' => false,
            ],
            [
                'slug' => 'los',
                'name' => 'Lagos',
                'region' => 'Nigeria',
                'address' => null,
                'email' => null,
                'phone' => null,
                'blurb' => 'Commercial hub for Nigeria, energy and technology matters, through an independent local partner.',
                'lat' => 6.5244,
                'lng' => 3.3792,
                'countries' => ['Nigeria'],
                'status' => 'partner_coverage',
                'relationship_type' => 'partnership',
                'is_headquarters' => false,
            ],
            [
                'slug' => 'par',
                'name' => 'Paris',
                'region' => 'France',
                'address' => null,
                'email' => null,
                'phone' => null,
                'blurb' => 'Gateway to European institutions, finance and francophone partners, through an independent local partner.',
                'lat' => 48.8566,
                'lng' => 2.3522,
                'countries' => ['France'],
                'status' => 'partner_coverage',
                'relationship_type' => 'partnership',
                'is_headquarters' => false,
            ],

            // --- New regional coverage (client brief, this dispatch) -----
            [
                'name' => 'Gulf Regional Office',
                'region' => 'Gulf / Middle East',
                'address' => null,
                'email' => null,
                'phone' => null,
                'blurb' => 'Coordinated partner coverage across the Gulf states.',
                'lat' => null,
                'lng' => null,
                'countries' => ['Bahrain', 'Kuwait', 'Oman', 'Qatar', 'United Arab Emirates'],
                'status' => 'partner_coverage',
                'relationship_type' => 'partnership',
                'is_headquarters' => false,
            ],
            [
                'name' => 'South Africa Regional Office',
                'region' => 'Southern Africa',
                'address' => null,
                'email' => null,
                'phone' => null,
                'blurb' => 'Coordinated partner coverage across Southern Africa.',
                'lat' => null,
                'lng' => null,
                'countries' => ['South Africa', 'Lesotho', 'Eswatini'],
                'status' => 'partner_coverage',
                'relationship_type' => 'partnership',
                'is_headquarters' => false,
            ],
            [
                'name' => 'Morocco Regional Office',
                'region' => 'North Africa',
                'address' => null,
                'email' => null,
                'phone' => null,
                'blurb' => 'Coordinated partner coverage across the countries listed below.',
                'lat' => null,
                'lng' => null,
                // NOTE: as specified in the client brief. Botswana, Malawi,
                // Namibia, Zambia and Zimbabwe are Southern Africa, not
                // geographically part of Morocco/North Africa — this
                // assignment was flagged as needing client confirmation.
                // Do not silently move it under a different regional
                // office; surface it for the client to confirm instead.
                'countries' => ['Morocco', 'Botswana', 'Malawi', 'Namibia', 'Zambia', 'Zimbabwe'],
                'status' => 'partner_coverage',
                'relationship_type' => 'partnership',
                'is_headquarters' => false,
            ],
            [
                'name' => 'Kenya Regional Office',
                'region' => 'East Africa',
                'address' => null,
                'email' => null,
                'phone' => null,
                'blurb' => 'Coordinated partner coverage across East Africa.',
                'lat' => null,
                'lng' => null,
                'countries' => ['Kenya', 'Burundi', 'Eritrea', 'Ethiopia', 'Rwanda', 'Somalia', 'Tanzania', 'Uganda'],
                'status' => 'partner_coverage',
                'relationship_type' => 'partnership',
                'is_headquarters' => false,
            ],
            [
                'name' => 'Asia + Pacific',
                'region' => 'Asia-Pacific',
                'address' => null,
                'email' => null,
                'phone' => null,
                'blurb' => 'Coverage under consideration. The specific countries and partner have not been confirmed.',
                'lat' => null,
                'lng' => null,
                // Left as a placeholder note rather than inventing
                // countries — the brief does not name any for this region.
                'countries' => ['Coverage area to be defined'],
                'status' => 'planned',
                'relationship_type' => 'partnership',
                'is_headquarters' => false,
            ],
            [
                'name' => 'Americas',
                'region' => 'North America',
                'address' => null,
                'email' => null,
                'phone' => null,
                // United States presence is already served by the
                // Washington, D.C. headquarters above (ActiveOffice); this
                // entry models a distinct, broader coverage ambition for
                // the wider Americas, with Canada proposed and not yet
                // confirmed. It does not change the HQ's status.
                'blurb' => 'United States coverage is confirmed through our Washington, D.C. headquarters; Canada coverage is proposed and not yet confirmed.',
                'lat' => null,
                'lng' => null,
                'countries' => ['United States', 'Canada'],
                'status' => 'planned',
                'relationship_type' => 'partnership',
                'is_headquarters' => false,
            ],
            [
                'name' => 'Europe',
                'region' => 'Europe',
                'address' => null,
                'email' => null,
                'phone' => null,
                // Separate from the existing Paris/France partner-coverage
                // entry above: the client brief marks a broader Europe
                // presence (France, UK, Switzerland, Germany) as merely
                // "proposed", so it is modelled here as Planned rather than
                // retroactively changing the Paris entry's status.
                'blurb' => 'A proposed, broader European presence. Not yet confirmed with a partner in any of the countries listed below.',
                'lat' => null,
                'lng' => null,
                'countries' => ['France', 'United Kingdom', 'Switzerland', 'Germany'],
                'status' => 'planned',
                'relationship_type' => 'partnership',
                'is_headquarters' => false,
            ],
        ];

        foreach ($offices as $position => $office) {
            $slug = $office['slug'] ?? Str::slug($office['name']);

            OfficeRecord::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $office['name'],
                    'region' => $office['region'],
                    'address' => $office['address'],
                    'email' => $office['email'],
                    'phone' => $office['phone'],
                    'blurb' => $office['blurb'],
                    'lat' => $office['lat'],
                    'lng' => $office['lng'],
                    'countries' => $office['countries'],
                    'status' => $office['status'],
                    'relationship_type' => $office['relationship_type'],
                    'is_headquarters' => $office['is_headquarters'],
                    'position' => $position + 1,
                ],
            );
        }
    }
}
