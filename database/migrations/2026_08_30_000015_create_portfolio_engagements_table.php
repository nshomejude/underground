<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_engagements', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('sector');
            $table->string('title');
            $table->text('summary');
            $table->string('outcome');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_engagements');
    }
};
