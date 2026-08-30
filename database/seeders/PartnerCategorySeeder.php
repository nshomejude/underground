<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Infrastructure\Persistence\Eloquent\Models\PartnerCategoryRecord;

/**
 * Seeds the partner categories shown on /partners, migrated from the
 * previously hardcoded PartnerController array.
 */
final class PartnerCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
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
        ];

        foreach ($categories as $position => $category) {
            PartnerCategoryRecord::query()->updateOrCreate(
                ['slug' => Str::slug($category['title'])],
                [
                    'icon' => $category['icon'],
                    'title' => $category['title'],
                    'body' => $category['body'],
                    'position' => $position + 1,
                ],
            );
        }
    }
}
