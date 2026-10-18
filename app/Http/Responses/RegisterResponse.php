<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Symfony\Component\HttpFoundation\Response;

class RegisterResponse implements RegisterResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  Request  $request
     */
    public function toResponse($request): Response
    {
        $user = $request->user();

        $tenantDomain = null;
        if ($user instanceof User && $user->tenant instanceof Tenant) {
            $domain = $user->tenant->getPrimaryDomain();
            if ($domain) {
                $scheme = $request->getScheme();
                $port = $request->getPort();
                $portPart = ($port && ! in_array($port, [80, 443], true)) ? ':'.$port : '';
                $tenantDomain = "{$scheme}://{$domain}{$portPart}";
            }
        }

        // Ensure user is not logged into the central / main panel
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($tenantDomain) {
            $query = http_build_query([
                'email' => $user->email,
                'status' => 'Workspace created successfully! Please sign in with your credentials.',
            ]);

            $targetUrl = "{$tenantDomain}/login?{$query}";
        } else {
            $targetUrl = route('login');
        }

        if ($request->wantsJson()) {
            return new JsonResponse(['redirect' => $targetUrl], 201);
        }

        return Inertia::location($targetUrl);
    }
}
