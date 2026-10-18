<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TenantDashboardController extends Controller
{
    /**
     * Show the tenant dashboard.
     */
    public function index(Request $request): Response
    {
        $tenant = tenant();
        $domain = $tenant instanceof Tenant ? $tenant->getPrimaryDomain() : null;

        return Inertia::render('tenant/Dashboard', [
            'tenant' => [
                'id' => tenant('id'),
                'name' => tenant('name') ?? tenant('id'),
                'domain' => $domain ?? request()->getHost(),
            ],
            'user' => [
                'id' => $request->user()?->id,
                'name' => $request->user()?->name,
                'email' => $request->user()?->email,
            ],
        ]);
    }
}
