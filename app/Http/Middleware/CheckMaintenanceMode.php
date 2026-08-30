<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Domain\Content\Repositories\SiteSettingRepository;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * When staff flip the "Maintenance Mode" toggle in Site Configuration,
 * every public visitor gets the branded 503 page (resources/views/errors/503)
 * instead of the real site. Staff themselves are never locked out: an
 * authenticated admin, and the /admin, /login, and asset routes needed to
 * reach and use the admin panel, all bypass the check.
 */
final class CheckMaintenanceMode
{
    public function __construct(private readonly SiteSettingRepository $settings) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('admin', 'admin/*', 'login', 'logout', 'build/*')) {
            return $next($request);
        }

        if ($request->user()?->is_admin) {
            return $next($request);
        }

        $setting = $this->settings->current();

        abort_if($setting->maintenanceMode, 503, $setting->maintenanceMessage ?: 'We\'re carrying out some quiet maintenance. Please check back in a few minutes.');

        return $next($request);
    }
}
