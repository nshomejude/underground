<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Infrastructure\Persistence\Eloquent\Models\CapabilityRecord;
use Infrastructure\Persistence\Eloquent\Models\EngagementModelRecord;
use Infrastructure\Persistence\Eloquent\Models\EventRecord;
use Infrastructure\Persistence\Eloquent\Models\InquiryRecord;
use Infrastructure\Persistence\Eloquent\Models\InsightRecord;
use Infrastructure\Persistence\Eloquent\Models\MembershipApplicationRecord;
use Infrastructure\Persistence\Eloquent\Models\MembershipTierRecord;
use Infrastructure\Persistence\Eloquent\Models\MetricRecord;
use Infrastructure\Persistence\Eloquent\Models\PartnerCategoryRecord;
use Infrastructure\Persistence\Eloquent\Models\PillarRecord;
use Infrastructure\Persistence\Eloquent\Models\PortfolioEngagementRecord;
use Infrastructure\Persistence\Eloquent\Models\ProjectRecord;
use Infrastructure\Persistence\Eloquent\Models\SectorRecord;
use Infrastructure\Persistence\Eloquent\Models\TeamMemberRecord;

/**
 * Landing page for staff at /admin: a KPI/overview dashboard summarising
 * the review queues and content library, plus quick links out to every
 * admin section contributed across the review-queue and content-admin
 * modules. Read-only — owns no data of its own.
 */
final class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $pendingApplications = MembershipApplicationRecord::query()
            ->whereIn('status', ['submitted', 'under_review'])
            ->count();

        $approvedApplications = MembershipApplicationRecord::query()->where('status', 'approved')->count();
        $declinedApplications = MembershipApplicationRecord::query()->where('status', 'declined')->count();
        $totalApplications = MembershipApplicationRecord::query()->count();
        $decidedApplications = $approvedApplications + $declinedApplications;

        $openInquiries = InquiryRecord::query()
            ->whereIn('status', ['received', 'under_review', 'engaged'])
            ->count();
        $totalInquiries = InquiryRecord::query()->count();

        $publishedInsights = InsightRecord::query()->whereNotNull('published_at')->count();
        $draftInsights = InsightRecord::query()->whereNull('published_at')->count();

        $upcomingEvents = EventRecord::query()
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->limit(5)
            ->get();

        $recentApplications = MembershipApplicationRecord::query()
            ->latest('created_at')
            ->limit(5)
            ->get();

        $recentInquiries = InquiryRecord::query()
            ->latest('created_at')
            ->limit(5)
            ->get();

        return view('admin.index', [
            'kpis' => [
                'pending_applications' => $pendingApplications,
                'approval_rate' => $decidedApplications > 0
                    ? (int) round($approvedApplications / $decidedApplications * 100)
                    : null,
                'open_inquiries' => $openInquiries,
                'total_inquiries' => $totalInquiries,
                'published_insights' => $publishedInsights,
                'draft_insights' => $draftInsights,
                'upcoming_events' => $upcomingEvents->count(),
                'total_users' => User::query()->count(),
            ],
            'pipeline' => [
                'applications' => [
                    'total' => $totalApplications,
                    'pending' => $pendingApplications,
                    'approved' => $approvedApplications,
                    'declined' => $declinedApplications,
                ],
                'inquiries' => [
                    'total' => $totalInquiries,
                    'open' => $openInquiries,
                    'resolved' => $totalInquiries - $openInquiries,
                ],
            ],
            'contentCounts' => [
                ['label' => 'Insights', 'route' => 'admin.insights.index', 'count' => $publishedInsights + $draftInsights],
                ['label' => 'Capabilities', 'route' => 'admin.capabilities.index', 'count' => CapabilityRecord::query()->count()],
                ['label' => 'Sectors', 'route' => 'admin.sectors.index', 'count' => SectorRecord::query()->count()],
                ['label' => 'Metrics', 'route' => 'admin.metrics.index', 'count' => MetricRecord::query()->count()],
                ['label' => 'Engagement Models', 'route' => 'admin.engagement-models.index', 'count' => EngagementModelRecord::query()->count()],
                ['label' => 'Pillars', 'route' => 'admin.pillars.index', 'count' => PillarRecord::query()->count()],
                ['label' => 'Membership Tiers', 'route' => 'admin.membership-tiers.index', 'count' => MembershipTierRecord::query()->count()],
                ['label' => 'Team', 'route' => 'admin.team.index', 'count' => TeamMemberRecord::query()->count()],
                ['label' => 'Partners', 'route' => 'admin.partners.index', 'count' => PartnerCategoryRecord::query()->count()],
                ['label' => 'Portfolio', 'route' => 'admin.portfolio.index', 'count' => PortfolioEngagementRecord::query()->count()],
                ['label' => 'Projects', 'route' => 'admin.projects.index', 'count' => ProjectRecord::query()->count()],
                ['label' => 'Events', 'route' => 'admin.events.index', 'count' => EventRecord::query()->count()],
            ],
            'upcomingEvents' => $upcomingEvents,
            'recentApplications' => $recentApplications,
            'recentInquiries' => $recentInquiries,
        ]);
    }
}
