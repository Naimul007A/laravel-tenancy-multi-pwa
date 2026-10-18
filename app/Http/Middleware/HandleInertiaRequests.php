<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;
use Symfony\Component\HttpFoundation\Response;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, \Closure $next): Response
    {
        $response = parent::handle($request, $next);

        if ($request->routeIs('pwa.manifest') || str_ends_with($request->path(), 'manifest.webmanifest')) {
            $response->headers->set('Vary', 'Host');
        }

        return $response;
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        /** @var Tenant|null $tenant */
        $tenant = tenancy()->initialized ? tenant() : null;

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'portal' => $request->is('staff*') ? 'staff' : ($request->is('users*') ? 'users' : null),
            'auth' => [
                'user' => $request->user(),
                'staff' => Auth::guard('staff')->user(),
                'web' => Auth::guard('web')->user(),
                'tenant' => $tenant ? [
                    'id' => $tenant->id,
                    'name' => $tenant->name ?? (string) $tenant->id,
                    'short_name' => $tenant->getShortName(),
                    'domain' => $tenant->getPrimaryDomain(),
                    'staff_theme_color' => $tenant->getStaffThemeColor(),
                    'user_theme_color' => $tenant->getUserThemeColor(),
                ] : null,
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
