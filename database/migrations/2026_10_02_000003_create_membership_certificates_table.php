<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_certificates', function (Blueprint $table): void {
            $table->id();
            $table->string('application_reference')->index();
            $table->string('member_id')->index();
            $table->string('serial')->nullable()->unique();
            $table->string('verify_token', 40)->unique();
            $table->timestamp('issued_at');
            $table->timestamp('valid_through');
            $table->timestamp('revoked_at')->nullable();
            $table->string('revoked_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_certificates');
    }
};
