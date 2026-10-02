<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\MemberProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<MemberProfile> */
final class MemberProfileFactory extends Factory
{
    protected $model = MemberProfile::class;

    public function definition(): array
    {
        $name = fake()->name();

        return [
            'user_id' => User::factory(),
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(10, 99999),
            'display_name' => $name,
            'visibility' => 'members',
            'open_to_collaboration' => true,
        ];
    }

    /** A profile that scores 100. */
    public function complete(): static
    {
        return $this->state(fn () => [
            'headline' => 'Infrastructure financier connecting capital to projects',
            'bio' => str_repeat('Experienced cross-border operator. ', 5),
            'avatar_path' => 'profiles/example.jpg',
            'city' => 'Lagos',
            'country' => 'Nigeria',
            'languages' => ['English', 'French'],
            'sectors' => ['energy-natural-resources'],
            'supply_chain_roles' => ['financier'],
            'seeking_kinds' => ['partner'],
            'seeking_summary' => 'Co-investors for grid projects.',
            'offering_summary' => 'Structured finance and origination.',
            'services' => [['title' => 'Project finance', 'description' => 'Debt structuring', 'sector' => 'energy-natural-resources']],
            'portfolio' => [['title' => '200MW solar', 'summary' => 'Financed', 'year' => '2024', 'client_type' => 'Utility', 'link' => null]],
            'website' => 'https://example.com',
            'organisation_name' => 'Example Capital',
            'organisation_role' => 'Managing Partner',
        ]);
    }
}
