<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('motions', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('summary', 400)->nullable();
            $table->text('body')->nullable();
            $table->string('kind')->default('decision');
            $table->unsignedTinyInteger('min_tier_rank')->default(1);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('draft')->index();
            $table->json('choices')->nullable();
            $table->unsignedInteger('quorum')->default(1);
            $table->decimal('pass_threshold', 5, 2)->default(50);
            $table->boolean('anonymous')->default(false);
            $table->timestamp('opens_at')->nullable();
            $table->timestamp('closes_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->string('outcome')->nullable();
            $table->json('result')->nullable();
            $table->timestamps();
        });

        Schema::create('votes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('motion_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('choice');
            $table->unsignedSmallInteger('weight')->default(1);
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['motion_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votes');
        Schema::dropIfExists('motions');
    }
};
