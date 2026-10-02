<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plan_change_requests', function (Blueprint $table): void {
            $table->unsignedBigInteger('amount_cents')->nullable()->after('note');
            $table->timestamp('applied_at')->nullable()->after('reviewed_at');
        });

        // Annual fee bands: USD 10,000 up to USD 10,000,000.
        $bands = [
            'corporate-affiliate' => [10_000, 100_000],
            'principal-circle' => [100_000, 1_000_000],
            'sovereign-partner' => [1_000_000, 10_000_000],
        ];

        foreach ($bands as $slug => [$min, $max]) {
            $row = DB::table('membership_plans')->where('slug', $slug)->first();
            if ($row === null) {
                continue;
            }
            $limits = json_decode((string) $row->limits, true) ?: [];
            $limits['price_max_cents'] = $max * 100;
            DB::table('membership_plans')->where('id', $row->id)->update([
                'price_cents' => $min * 100,
                'currency' => 'USD',
                'billing_interval' => 'year',
                'limits' => json_encode($limits),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('plan_change_requests', function (Blueprint $table): void {
            $table->dropColumn(['amount_cents', 'applied_at']);
        });
    }
};
