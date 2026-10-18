<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PwaManifestController extends Controller
{
    /**
     * Handle the incoming manifest request.
     */
    public function __invoke(Request $request, string $portal): JsonResponse
    {
        abort_unless(in_array($portal, ['staff', 'users'], true), 404);

        $tenant = tenant();
        $tenantId = $tenant instanceof Tenant ? (string) $tenant->id : 'tenant';
        $tenantName = $tenant instanceof Tenant ? $tenant->getShortName() : 'Acme High';
        $shortName = $tenantName;

        if ($portal === 'staff') {
            $themeColor = $tenant instanceof Tenant ? $tenant->getStaffThemeColor() : '#0f172a';
            $icon192 = $tenant instanceof Tenant ? $tenant->getStaffIcon192() : '/icons/staff-192x192.png';
            $icon512 = $tenant instanceof Tenant ? $tenant->getStaffIcon512() : '/icons/staff-512x512.png';

            $payload = [
                'id' => "/staff/?tenant={$tenantId}",
                'name' => "{$tenantName} - Staff & Teachers",
                'short_name' => "{$shortName} Staff",
                'start_url' => '/staff/',
                'scope' => '/staff/',
                'display' => 'standalone',
                'background_color' => '#ffffff',
                'theme_color' => $themeColor,
                'icons' => [
                    [
                        'src' => $icon192,
                        'sizes' => '192x192',
                        'type' => 'image/png',
                        'purpose' => 'any',
                    ],
                    [
                        'src' => $icon512,
                        'sizes' => '512x512',
                        'type' => 'image/png',
                        'purpose' => 'maskable',
                    ],
                ],
            ];
        } else {
            $themeColor = $tenant instanceof Tenant ? $tenant->getUserThemeColor() : '#4f46e5';
            $icon192 = $tenant instanceof Tenant ? $tenant->getUserIcon192() : '/icons/user-192x192.png';
            $icon512 = $tenant instanceof Tenant ? $tenant->getUserIcon512() : '/icons/user-512x512.png';

            $payload = [
                'id' => "/users/?tenant={$tenantId}",
                'name' => "{$tenantName} - Student & Parent Portal",
                'short_name' => $shortName,
                'start_url' => '/users/',
                'scope' => '/users/',
                'display' => 'standalone',
                'background_color' => '#ffffff',
                'theme_color' => $themeColor,
                'icons' => [
                    [
                        'src' => $icon192,
                        'sizes' => '192x192',
                        'type' => 'image/png',
                        'purpose' => 'any',
                    ],
                    [
                        'src' => $icon512,
                        'sizes' => '512x512',
                        'type' => 'image/png',
                        'purpose' => 'maskable',
                    ],
                ],
            ];
        }

        return response()->json($payload, 200, [
            'Content-Type' => 'application/manifest+json; charset=utf-8',
            'Vary' => 'Host',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
