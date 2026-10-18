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

class UserAuthController extends Controller
{
    /**
     * Show the student & parent portal login page.
     */
    public function showLogin(Request $request): Response
    {
        $tenant = tenant();
        $domain = $tenant instanceof Tenant ? $tenant->getPrimaryDomain() : null;

        return Inertia::render('tenant/users/Login', [
            'tenant' => [
                'id' => tenant('id'),
                'name' => $tenant instanceof Tenant ? $tenant->getShortName() : (tenant('name') ?? tenant('id')),
                'short_name' => $tenant instanceof Tenant ? $tenant->getShortName() : tenant('id'),
                'domain' => $domain ?? $request->getHost(),
                'theme_color' => $tenant instanceof Tenant ? $tenant->getUserThemeColor() : '#4f46e5',
            ],
            'status' => session('status') ?? $request->query('status'),
            'initialEmail' => (string) $request->query('email', ''),
        ]);
    }

    /**
     * Authenticate student/parent user.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! $user->isUser()) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }

        if (! Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('tenant.users.dashboard'));
    }

    /**
     * Log out of the student/parent portal only.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        return redirect()->route('tenant.users.login');
    }
}
