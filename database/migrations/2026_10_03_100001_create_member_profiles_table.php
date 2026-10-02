<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('slug')->nullable()->unique();
            $table->string('display_name')->nullable();
            $table->string('headline', 160)->nullable();
            $table->text('bio')->nullable();
            $table->string('avatar_path')->nullable();
            $table->string('cover_path')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('timezone')->nullable();
            $table->json('languages')->nullable();
            $table->json('sectors')->nullable();
            $table->json('supply_chain_roles')->nullable();
            $table->json('seeking_kinds')->nullable();
            $table->text('seeking_summary')->nullable();
            $table->text('offering_summary')->nullable();
            $table->json('services')->nullable();
            $table->json('portfolio')->nullable();
            $table->string('website')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('public_email')->nullable();
            $table->string('organisation_name')->nullable();
            $table->string('organisation_role')->nullable();
            $table->string('organisation_size')->nullable();
            $table->string('organisation_website')->nullable();
            $table->string('visibility')->default('members');
            $table->boolean('open_to_collaboration')->default(true);
            $table->unsignedTinyInteger('completeness')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_profiles');
    }
};
