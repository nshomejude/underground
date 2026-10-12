<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Extends PortfolioEngagement with the fields a /team/case-studies entry
 * needs beyond the original /portfolio card: the firm's real role, the
 * concrete deliverables, the engagement period (left null where the
 * source material never stated one — see PortfolioEngagementSeeder), and
 * a note on publication agreement. Purely additive: the original
 * sector/title/summary/outcome columns are untouched, so /portfolio keeps
 * rendering unchanged.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portfolio_engagements', function (Blueprint $table): void {
            $table->string('real_role')->nullable()->after('outcome');
            $table->json('deliverables')->nullable()->after('real_role');
            $table->string('period')->nullable()->after('deliverables');
            $table->text('publication_agreement_note')->nullable()->after('period');
        });
    }

    public function down(): void
    {
        Schema::table('portfolio_engagements', function (Blueprint $table): void {
            $table->dropColumn(['real_role', 'deliverables', 'period', 'publication_agreement_note']);
        });
    }
};
