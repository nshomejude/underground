<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('identity_verifications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('draft')->index();
            $table->string('document_type')->nullable();
            $table->string('document_country')->nullable();
            $table->string('document_number_last4', 8)->nullable();
            $table->string('full_name_on_document')->nullable();
            $table->text('date_of_birth_encrypted')->nullable();
            $table->date('document_expiry')->nullable();
            $table->json('files')->nullable();
            $table->timestamp('consent_at')->nullable();
            $table->string('consent_version')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('reviewer_notes')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('identity_verifications');
    }
};
