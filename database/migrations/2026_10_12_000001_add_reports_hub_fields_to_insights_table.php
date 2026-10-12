<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Reports Hub (the renamed, repositioned successor to Insights) needs
 * richer editorial metadata per item: author, country, sector and a simple
 * public/members-only access level. This extends the existing `insights`
 * table rather than creating a parallel content type -- it is the same
 * aggregate with more structure. Defaults keep the migration safe to apply
 * against any pre-existing rows.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('insights', function (Blueprint $table) {
            $table->string('author')->default('Underground Network Research');
            $table->string('country')->default('Global');
            $table->string('sector')->default('Cross-Sector');
            $table->string('access_level')->default('public');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('insights', function (Blueprint $table) {
            $table->dropColumn(['author', 'country', 'sector', 'access_level']);
        });
    }
};
