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
        // $this->call(ContentSeeder::class);

        // Insights context: published thought-leadership pieces.
        // $this->call(InsightSeeder::class);

        // Engagement context: sample confidential inquiries (non-production
        // environments only — never seed real client approaches).
        // $this->call(InquirySeeder::class);

        // Membership context: membership tiers (Sovereign Partner, Principal
        // Circle, Corporate Affiliate) and any sample applications.
        // $this->call(MembershipSeeder::class);
    }
}
