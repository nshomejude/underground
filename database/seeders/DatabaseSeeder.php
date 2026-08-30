<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Content context: capabilities, sectors, metrics, engagement
        // models, pillars, and the single narrative row.
        $this->call(NarrativeSeeder::class);
        $this->call(CapabilitySeeder::class);
        $this->call(SectorSeeder::class);
        $this->call(MetricSeeder::class);
        $this->call(EngagementModelSeeder::class);
        $this->call(PillarSeeder::class);

        // Insights context: published thought-leadership pieces.
        $this->call(InsightSeeder::class);

        // Engagement context: confidential inquiries are user-generated and
        // are never seeded.

        // Membership context: the vetted tiers Underground extends.
        // Applications are user-generated and are never seeded, except for
        // the two demo accounts below that exercise the member account area.
        $this->call(MembershipTierSeeder::class);

        // Demo/seed-only member accounts (see class doc for credentials) so
        // /account can be exercised end-to-end without a staff approval UI.
        $this->call(MembershipAccountDemoSeeder::class);

        // Demo/seed-only staff admin account (see class doc for credentials)
        // so the /admin review queue can be exercised end-to-end.
        $this->call(AdminUserSeeder::class);
    }
}
