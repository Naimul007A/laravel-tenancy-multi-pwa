<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class StaffDashboardController extends Controller
{
    /**
     * Show the staff portal dashboard.
     */
    public function index(Request $request): Response
    {
        $tenant = tenant();
        $domain = $tenant instanceof Tenant ? $tenant->getPrimaryDomain() : null;
        $user = Auth::guard('staff')->user();

        return Inertia::render('tenant/staff/Dashboard', [
            'tenant' => [
                'id' => tenant('id'),
                'name' => $tenant instanceof Tenant ? $tenant->getShortName() : (tenant('name') ?? tenant('id')),
                'short_name' => $tenant instanceof Tenant ? $tenant->getShortName() : tenant('id'),
                'domain' => $domain ?? $request->getHost(),
                'theme_color' => $tenant instanceof Tenant ? $tenant->getStaffThemeColor() : '#0f172a',
            ],
            'user' => [
                'id' => $user?->id,
                'name' => $user?->name,
                'email' => $user?->email,
                'role' => $user?->role,
            ],
        ]);
    }
}
