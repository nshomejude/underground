<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds the richer detail-page structure each capability now needs —
     * see Domain\Content\Entities\Capability. Columns are nullable at the
     * schema level (so this applies cleanly against a table that already
     * has rows) even though the domain entity treats problemStatement,
     * audiences, method, deliverables and nextAction as always-present;
     * the repository fills in an empty fallback for any row authored
     * before this migration ran.
     */
    public function up(): void
    {
        Schema::table('capabilities', function (Blueprint $table): void {
            $table->text('problem_statement')->nullable()->after('summary');
            $table->json('audiences')->nullable()->after('problem_statement');
            $table->text('method')->nullable()->after('audiences');
            $table->json('deliverables')->nullable()->after('method');
            $table->string('confirmed_lead')->nullable()->after('deliverables');
            $table->string('authorized_reference')->nullable()->after('confirmed_lead');
            $table->string('next_action')->nullable()->after('authorized_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('capabilities', function (Blueprint $table): void {
            $table->dropColumn([
                'problem_statement',
                'audiences',
                'method',
                'deliverables',
                'confirmed_lead',
                'authorized_reference',
                'next_action',
            ]);
        });
    }
};
