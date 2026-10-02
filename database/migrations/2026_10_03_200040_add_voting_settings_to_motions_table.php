<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('motions', function (Blueprint $table): void {
            $table->boolean('weighted')->default(false);
            $table->boolean('allow_change')->default(true);
            // after_close = results stay sealed until the motion closes; live = visible while open.
            $table->string('show_results')->default('after_close');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('notified_at')->nullable();
            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamp('closed_notified_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->index(['status', 'closes_at']);
        });
    }

    public function down(): void
    {
        Schema::table('motions', function (Blueprint $table): void {
            $table->dropIndex(['status', 'closes_at']);
            $table->dropColumn(['weighted', 'allow_change', 'show_results', 'published_at', 'notified_at', 'reminder_sent_at', 'closed_notified_at', 'cancelled_at', 'cancel_reason']);
        });
    }
};
