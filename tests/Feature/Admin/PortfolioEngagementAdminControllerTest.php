<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Models\PortfolioEngagementRecord;
use Tests\TestCase;

final class PortfolioEngagementAdminControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_admin_can_create_an_engagement_with_case_study_fields(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.portfolio.store'), [
            'slug' => 'new-engagement',
            'title' => 'New Engagement',
            'sector' => 'Finance & Investments',
            'summary' => 'Context for the engagement.',
            'outcome' => 'A demonstrable result.',
            'real_role' => 'Retained advisor.',
            'deliverables' => "Strategy memo\nStakeholder plan\n\n",
            'period' => '6 months',
            'publication_agreement_note' => 'No publication agreement on file.',
            'position' => 7,
        ]);

        $response->assertRedirect(route('admin.portfolio.index'));
        $this->assertDatabaseHas('portfolio_engagements', [
            'slug' => 'new-engagement',
            'real_role' => 'Retained advisor.',
            'period' => '6 months',
        ]);

        $engagement = PortfolioEngagementRecord::query()->where('slug', 'new-engagement')->first();
        $this->assertSame(['Strategy memo', 'Stakeholder plan'], $engagement->deliverables);
    }

    public function test_case_study_fields_are_optional(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.portfolio.store'), [
            'slug' => 'minimal-engagement',
            'title' => 'Minimal Engagement',
            'sector' => 'Finance & Investments',
            'summary' => 'Context.',
            'outcome' => 'Result.',
            'position' => 8,
        ]);

        $response->assertRedirect(route('admin.portfolio.index'));
        $this->assertDatabaseHas('portfolio_engagements', ['slug' => 'minimal-engagement']);
    }
}
