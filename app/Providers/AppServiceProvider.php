<?php

namespace App\Providers;

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
        // STUB: a sibling module owns the real admin foundation, this may be
        // superseded at merge time.
        Gate::define('admin', fn ($user): bool => (bool) $user->is_admin);
    }
}
