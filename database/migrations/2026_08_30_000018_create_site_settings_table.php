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
     * Site-wide configuration — see Domain\Content\Entities\SiteSetting.
     * In practice this table holds a single row; there is no natural
     * unique key beyond "the current one", which the repository resolves
     * by taking the latest row rather than by a constraint here.
     */
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name');
            $table->string('site_tagline');
            $table->string('contact_email');
            $table->string('contact_phone')->nullable();
            $table->json('social_links');
            $table->string('footer_note')->nullable();
            $table->string('meta_title');
            $table->text('meta_description');
            $table->string('og_image_url')->nullable();
            $table->string('twitter_handle')->nullable();
            $table->boolean('maintenance_mode')->default(false);
            $table->text('maintenance_message')->nullable();
            $table->boolean('public_registration_enabled')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
