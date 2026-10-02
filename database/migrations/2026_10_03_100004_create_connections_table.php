<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('connections', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('addressee_id')->constrained('users')->cascadeOnDelete();
            $table->string('kind')->default('connect');
            $table->string('status')->default('pending')->index();
            $table->text('message')->nullable();
            $table->string('topic')->nullable();
            $table->json('shared_sectors')->nullable();
            $table->unsignedSmallInteger('match_score')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->unique(['requester_id', 'addressee_id']);
            $table->index('addressee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('connections');
    }
};
