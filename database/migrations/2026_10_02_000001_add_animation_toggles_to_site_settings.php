<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->boolean('gear_animation_enabled')->default(true)->after('public_registration_enabled');
            $table->boolean('network_animation_enabled')->default(true)->after('gear_animation_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->dropColumn(['gear_animation_enabled', 'network_animation_enabled']);
        });
    }
};
