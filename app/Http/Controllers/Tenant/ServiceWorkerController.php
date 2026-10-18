<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class ServiceWorkerController extends Controller
{
    /**
     * Serve the root Service Worker script with Service-Worker-Allowed header.
     */
    public function __invoke(): Response
    {
        $swPath = public_path('sw.js');
        $content = file_exists($swPath)
            ? (string) file_get_contents($swPath)
            : '// Root Service Worker fallback';

        return response($content, 200, [
            'Content-Type' => 'application/javascript; charset=utf-8',
            'Service-Worker-Allowed' => '/',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }
}
