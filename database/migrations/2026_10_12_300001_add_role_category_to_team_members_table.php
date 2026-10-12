<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('team_members', function (Blueprint $table): void {
            $table->string('role_category')->default('expertise')->after('has_portrait');
            $table->string('employment_status')->default('employee')->after('role_category');
            $table->json('languages')->nullable()->after('employment_status');
        });
    }

    public function down(): void
    {
        Schema::table('team_members', function (Blueprint $table): void {
            $table->dropColumn(['role_category', 'employment_status', 'languages']);
        });
    }
};
