<?php

declare(strict_types=1);

namespace Tests\Feature\Membership;

use Database\Seeders\MembershipTierSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class MembershipCardsPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(MembershipTierSeeder::class);
    }

    public function test_the_cards_page_renders_a_sample_card_for_every_tier(): void
    {
        $response = $this->get(route('membership.cards'))
            ->assertOk()
            ->assertSee('Sovereign Partner')
            ->assertSee('Principal Circle')
            ->assertSee('Corporate Affiliate');

        // A permanent member id, shaped like "UG · 2018 · 000012" — a brand
        // prefix, the four-digit join year, and a zero-padded sequential
        // number — distinct from the "UGM-2026-7KQ4XB" application reference.
        $this->assertMatchesRegularExpression(
            '/UG\s*·\s*\d{4}\s*·\s*\d{6}/u',
            $response->getContent(),
        );
    }

    public function test_the_cards_page_is_linked_from_the_membership_index(): void
    {
        $this->get(route('membership.index'))
            ->assertOk()
            ->assertSee(route('membership.cards'), false);
    }
}
