<?php

declare (strict_types = 1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class TenantAuthController extends Controller {
    /**
     * Show the tenant login page.
     */
    public function showLogin(): Response {
        $tenant = tenant();
        $domain = $tenant instanceof Tenant ? $tenant->getPrimaryDomain() : null;

        return Inertia::render('tenant/Login', [
            'tenant' => [
                'id'     => tenant('id'),
                'name'   => tenant('name') ?? tenant('id'),
                'domain' => $domain ?? request()->getHost(),
            ],
            'status' => session('status'),
        ]);
    }

    /**
     * Authenticate the tenant user.
     */
    public function login(Request $request): RedirectResponse {
        $credentials = $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('tenant.dashboard'));
    }

    /**
     * Log the tenant user out.
     */
    public function logout(Request $request): RedirectResponse {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('tenant.login');
    }
}
