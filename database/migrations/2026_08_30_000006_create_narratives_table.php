<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The firm's authored brand copy — see Domain\Content\Entities\Narrative.
     * In practice this table holds a single row; there is no natural unique
     * key beyond "the current one", which the repository resolves by taking
     * the latest row rather than by a constraint here.
     */
    public function up(): void
    {
        Schema::create('narratives', function (Blueprint $table) {
            $table->id();
            $table->string('company');
            $table->string('tagline');
            $table->string('eyebrow');
            $table->json('headline');
            $table->string('accent_line');
            $table->text('intro');
            $table->json('primary_cta');
            $table->json('secondary_cta');
            $table->string('creed_title');
            $table->text('creed_body');
            $table->string('capabilities_eyebrow');
            $table->string('capabilities_heading');
            $table->string('sectors_heading');
            $table->string('reach_heading');
            $table->text('reach_body');
            $table->json('reach_cta');
            $table->string('engagement_heading');
            $table->string('closing_heading');
            $table->text('closing_support');
            $table->json('closing_cta');
            $table->json('navigation');
            $table->string('copyright');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('narratives');
    }
};
