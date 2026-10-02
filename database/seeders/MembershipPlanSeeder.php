<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\MembershipPlan;
use Illuminate\Database\Seeder;

/**
 * The three real membership tiers as comparable plans. Idempotent: keyed on
 * slug, and it never overwrites a plan staff have already edited (price,
 * copy) once it exists. Pricing is by application, so price_cents stays NULL.
 */
final class MembershipPlanSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->plans() as $plan) {
            MembershipPlan::firstOrCreate(['slug' => $plan['slug']], $plan);
        }
    }

    /** @return list<array<string, mixed>> */
    private function plans(): array
    {
        return [
            [
                'slug' => 'corporate-affiliate',
                'tier_slug' => 'corporate-affiliate',
                'name' => 'Corporate Affiliate',
                'tagline' => 'A trusted seat in the network for organisations and operators.',
                'price_cents' => 1000000,
                'billing_interval' => 'year',
                'features' => [
                    'Membership card and certificate of membership',
                    'Listing in the member directory once identity is verified',
                    'Up to 10 connection requests per month',
                    'Private messaging with your accepted connections',
                    'Votes on affiliate-level motions',
                    'Verified identity and verified company badges',
                    'Standard response to private inquiries',
                ],
                'limits' => [
                    'price_max_cents' => 10000000,
                    'connection_requests_per_month' => 10,
                    'vote_rank' => 1,
                    'can_create_motions' => false,
                    'forum_access' => 'none',
                    'inquiry_response' => 'standard',
                ],
                'is_featured' => false,
                'position' => 1,
            ],
            [
                'slug' => 'principal-circle',
                'tier_slug' => 'principal-circle',
                'name' => 'Principal Circle',
                'tagline' => 'For principals who shape decisions and want wider reach.',
                'price_cents' => 10000000,
                'billing_interval' => 'year',
                'features' => [
                    'Membership card and certificate of membership',
                    'Listing in the member directory once identity is verified',
                    'Up to 30 connection requests per month',
                    'Private messaging with your accepted connections',
                    'Votes on affiliate and principal-level motions',
                    'Consideration for selected invitation-only forums',
                    'Verified identity and verified company badges',
                    'Priority response to private inquiries',
                ],
                'limits' => [
                    'price_max_cents' => 100000000,
                    'connection_requests_per_month' => 30,
                    'vote_rank' => 2,
                    'can_create_motions' => false,
                    'forum_access' => 'selected',
                    'inquiry_response' => 'priority',
                ],
                'is_featured' => true,
                'position' => 2,
            ],
            [
                'slug' => 'sovereign-partner',
                'tier_slug' => 'sovereign-partner',
                'name' => 'Sovereign Partner',
                'tagline' => 'The highest standing: institutions and governments at the table.',
                'price_cents' => 100000000,
                'billing_interval' => 'year',
                'features' => [
                    'Membership card and certificate of membership',
                    'Listing in the member directory once identity is verified',
                    'Unlimited connection requests',
                    'Private messaging with your accepted connections',
                    'Votes on every motion, and the right to open motions',
                    'Standing access to invitation-only forums',
                    'Verified identity and verified company badges',
                    'Priority response to private inquiries, with a named partner',
                ],
                'limits' => [
                    'price_max_cents' => 1000000000,
                    'connection_requests_per_month' => null,
                    'vote_rank' => 3,
                    'can_create_motions' => true,
                    'forum_access' => 'all',
                    'inquiry_response' => 'dedicated',
                ],
                'is_featured' => false,
                'position' => 3,
            ],
        ];
    }
}
