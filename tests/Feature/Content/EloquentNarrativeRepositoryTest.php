<?php

declare(strict_types=1);

namespace Tests\Feature\Content;

use Domain\Content\Entities\Narrative;
use Domain\Shared\Exceptions\DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Models\NarrativeRecord;
use Infrastructure\Persistence\Eloquent\Repositories\EloquentNarrativeRepository;
use Tests\TestCase;

final class EloquentNarrativeRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_current_throws_when_no_narrative_has_been_authored(): void
    {
        $repository = new EloquentNarrativeRepository;

        $this->expectException(DomainException::class);

        $repository->current();
    }

    public function test_current_maps_the_latest_narrative_row_to_the_entity(): void
    {
        NarrativeRecord::factory()->create([
            'company' => 'Underground',
            'tagline' => 'Strategic Influence. Real Outcomes.',
            'headline' => ['Power Beneath', 'the Surface.'],
            'navigation' => [
                ['label' => 'Capabilities', 'href' => '#capabilities'],
            ],
        ]);

        $repository = new EloquentNarrativeRepository;

        $narrative = $repository->current();

        $this->assertSame('Underground', $narrative->company);
        $this->assertSame('Strategic Influence. Real Outcomes.', $narrative->tagline);
        $this->assertSame(['Power Beneath', 'the Surface.'], $narrative->headline);
        $this->assertSame([['label' => 'Capabilities', 'href' => '#capabilities']], $narrative->navigation);
    }

    public function test_current_returns_the_most_recently_created_row(): void
    {
        NarrativeRecord::factory()->create(['tagline' => 'Old Tagline']);
        NarrativeRecord::factory()->create(['tagline' => 'Newest Tagline']);

        $repository = new EloquentNarrativeRepository;

        $this->assertSame('Newest Tagline', $repository->current()->tagline);
    }

    public function test_update_creates_the_first_row_when_none_exists_yet(): void
    {
        $repository = new EloquentNarrativeRepository;

        $repository->update($this->narrativeWith(['tagline' => 'Freshly Authored']));

        $this->assertSame(1, NarrativeRecord::count());
        $this->assertSame('Freshly Authored', $repository->current()->tagline);
    }

    public function test_update_overwrites_the_existing_row_in_place(): void
    {
        NarrativeRecord::factory()->create(['tagline' => 'Original Tagline']);

        $repository = new EloquentNarrativeRepository;

        $repository->update($this->narrativeWith(['tagline' => 'Revised Tagline']));

        $this->assertSame(1, NarrativeRecord::count());
        $this->assertSame('Revised Tagline', $repository->current()->tagline);
    }

    /** @param array<string, mixed> $overrides */
    private function narrativeWith(array $overrides): Narrative
    {
        $defaults = [
            'company' => 'Underground',
            'tagline' => 'Strategic Influence. Real Outcomes.',
            'eyebrow' => 'The Firm',
            'headline' => ['Power Beneath', 'the Surface.'],
            'accentLine' => 'An accent line.',
            'intro' => 'An intro paragraph.',
            'primaryCta' => ['label' => 'Start a confidential conversation', 'href' => '#inquiry'],
            'secondaryCta' => ['label' => 'Explore capabilities', 'href' => '#capabilities'],
            'creedTitle' => 'Our Creed',
            'creedBody' => 'A creed body.',
            'capabilitiesEyebrow' => 'What We Do',
            'capabilitiesHeading' => 'Capabilities Heading',
            'sectorsHeading' => 'Sectors Heading',
            'reachHeading' => 'Reach Heading',
            'reachBody' => 'A reach body.',
            'reachCta' => ['label' => 'View our reach', 'href' => '#reach'],
            'engagementHeading' => 'Engagement Heading',
            'closingHeading' => 'Closing Heading',
            'closingSupport' => 'A closing support paragraph.',
            'closingCta' => ['label' => 'Start a confidential conversation', 'href' => '#inquiry'],
            'navigation' => [['label' => 'Capabilities', 'href' => '#capabilities']],
            'copyright' => '© 2026 Underground. All rights reserved.',
        ];

        $attributes = array_merge($defaults, $overrides);

        return new Narrative(...$attributes);
    }
}
