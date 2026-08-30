<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * DEMO/SEED-ONLY staff account so the admin review queue can be exercised
 * end-to-end without a real staff directory.
 *
 * This is a well-known placeholder credential, not a real secret:
 *
 *   Staff admin   admin@underground.example / admin-password
 */
final class AdminUserSeeder extends Seeder
{
    private const DEMO_PASSWORD = 'admin-password';

    public function run(): void
    {
        $user = User::query()->firstOrCreate(
            ['email' => 'admin@underground.example'],
            [
                'name' => 'Underground Staff Admin',
                'password' => Hash::make(self::DEMO_PASSWORD),
                'email_verified_at' => now(),
            ],
        );

        // is_admin is deliberately not mass-assignable (see App\Models\User)
        // so a form submission can never grant it; set it directly here.
        $user->forceFill(['is_admin' => true])->save();
    }
}
