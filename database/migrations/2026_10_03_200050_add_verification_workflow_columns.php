<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['identity_verifications', 'company_verifications'] as $name) {
            Schema::table($name, function (Blueprint $table): void {
                $table->unsignedTinyInteger('last_step')->default(1);
                $table->text('info_request')->nullable();
                $table->timestamp('in_review_at')->nullable();
                $table->timestamp('files_purged_at')->nullable();
            });
        }

        Schema::create('verification_events', function (Blueprint $table): void {
            $table->id();
            $table->string('kind', 16);
            $table->unsignedBigInteger('verification_id');
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event', 40);
            $table->text('note')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index(['kind', 'verification_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verification_events');
        foreach (['identity_verifications', 'company_verifications'] as $name) {
            Schema::table($name, function (Blueprint $table): void {
                $table->dropColumn(['last_step', 'info_request', 'in_review_at', 'files_purged_at']);
            });
        }
    }
};
