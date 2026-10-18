<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class StaffAuthController extends Controller
{
    /**
     * Show the staff portal login page.
     */
    public function showLogin(Request $request): Response
    {
        $tenant = tenant();
        $domain = $tenant instanceof Tenant ? $tenant->getPrimaryDomain() : null;

        return Inertia::render('tenant/staff/Login', [
            'tenant' => [
                'id' => tenant('id'),
                'name' => $tenant instanceof Tenant ? $tenant->getShortName() : (tenant('name') ?? tenant('id')),
                'short_name' => $tenant instanceof Tenant ? $tenant->getShortName() : tenant('id'),
                'domain' => $domain ?? $request->getHost(),
                'theme_color' => $tenant instanceof Tenant ? $tenant->getStaffThemeColor() : '#0f172a',
            ],
            'status' => session('status') ?? $request->query('status'),
            'initialEmail' => (string) $request->query('email', ''),
        ]);
    }

    /**
     * Authenticate staff member.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! $user->isStaff()) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }

        if (! Auth::guard('staff')->attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('tenant.staff.dashboard'));
    }

    /**
     * Log out of the staff portal only.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('staff')->logout();

        return redirect()->route('tenant.staff.login');
    }
}
