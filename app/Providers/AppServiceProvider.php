<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Backs the `admin` route middleware alias (see bootstrap/app.php):
        // logged-in staff with is_admin=true may pass, everyone else is
        // refused. Guests never reach the Gate check at all — the `auth`
        // middleware, applied alongside `admin` on every /admin route,
        // redirects them to /login first.
        Gate::define('admin', fn (User $user): bool => $user->is_admin);
    }
}
