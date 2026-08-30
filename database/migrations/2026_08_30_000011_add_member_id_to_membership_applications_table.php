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
     * member_id is the permanent card credential assigned once, at approval
     * (see Domain\Membership\ValueObjects\MemberId) — distinct from
     * `reference`, the opaque tracking handle assigned at submission. It is
     * nullable because most applications never reach Approved.
     */
    public function up(): void
    {
        Schema::table('membership_applications', function (Blueprint $table) {
            $table->string('member_id')->nullable()->unique()->after('reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membership_applications', function (Blueprint $table) {
            $table->dropUnique(['member_id']);
            $table->dropColumn('member_id');
        });
    }
};
