<?php

declare(strict_types=1);

namespace Tests\Feature\Content;

use Tests\TestCase;

final class LegalPagesTest extends TestCase
{
    public function test_terms_page_renders(): void
    {
        $this->get(route('terms'))
            ->assertOk()
            ->assertSee('Terms of Service')
            ->assertSee('Limitation of Liability')
            ->assertSee('Governing Law');
    }

    public function test_privacy_page_renders(): void
    {
        $this->get(route('privacy'))
            ->assertOk()
            ->assertSee('Privacy Policy')
            ->assertSee('Retention')
            ->assertSee('privacy@underground-network.example');
    }
}
