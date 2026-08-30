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
     * A MembershipApplication mirrors ConfidentialInquiry's aggregate shape:
     * submitted -> under_review -> approved / declined. References carry the
     * "UGM-" prefix (e.g. UGM-2026-7KQ4XB) to distinguish them from
     * confidential-inquiry references ("UG-").
     */
    public function up(): void
    {
        Schema::create('membership_applications', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('tier_id')->constrained('membership_tiers');
            $table->string('applicant_name');
            $table->string('organisation')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('country')->nullable();
            $table->text('statement');
            $table->string('status');
            $table->timestamp('submitted_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_applications');
    }
};
