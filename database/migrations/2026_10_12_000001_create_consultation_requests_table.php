<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('consultation_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('name');
            $table->string('organisation')->nullable();
            $table->string('role')->nullable();
            $table->string('email');
            $table->string('country')->nullable();
            $table->string('sector');
            $table->string('maturity_stage');
            $table->text('project_description');
            $table->text('need_description');
            $table->string('budget_range');
            $table->boolean('has_supporting_documents')->default(false);
            $table->string('status');
            $table->timestamp('submitted_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultation_requests');
    }
};
