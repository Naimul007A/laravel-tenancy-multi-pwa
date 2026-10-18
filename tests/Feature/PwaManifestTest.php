<?php

declare(strict_types=1);

use App\Models\Tenant;

beforeEach(function () {
    $this->tenant = Tenant::create([
        'id' => 'acme',
        'name' => 'Acme High School',
        'short_name' => 'Acme High',
        'staff_theme_color' => '#0f172a',
        'user_theme_color' => '#4f46e5',
        'staff_icon_192' => '/icons/staff-192x192.png',
        'staff_icon_512' => '/icons/staff-512x512.png',
        'user_icon_192' => '/icons/user-192x192.png',
        'user_icon_512' => '/icons/user-512x512.png',
    ]);

    $this->tenant->createDomain(['domain' => 'acme.test']);

    $this->tenantB = Tenant::create([
        'id' => 'globex',
        'name' => 'Globex Academy',
        'short_name' => 'Globex Academy',
        'staff_theme_color' => '#1e293b',
        'user_theme_color' => '#059669',
        'staff_icon_192' => '/icons/globex-staff-192.png',
        'staff_icon_512' => '/icons/globex-staff-512.png',
        'user_icon_192' => '/icons/globex-user-192.png',
        'user_icon_512' => '/icons/globex-user-512.png',
    ]);

    $this->tenantB->createDomain(['domain' => 'globex.test']);
});

test('TC-01: Tenant A staff manifest returns correct payload, scope, theme, and icons', function () {
    $response = $this->get('http://acme.test/staff/manifest.webmanifest');

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/manifest+json; charset=utf-8');
    $response->assertHeader('Vary', 'Host');

    $manifest = $response->json();

    expect($manifest['id'])->toBe('/staff/?tenant=acme')
        ->and($manifest['name'])->toBe('Acme High - Staff & Teachers')
        ->and($manifest['short_name'])->toBe('Acme High Staff')
        ->and($manifest['start_url'])->toBe('/staff/')
        ->and($manifest['scope'])->toBe('/staff/')
        ->and($manifest['display'])->toBe('standalone')
        ->and($manifest['theme_color'])->toBe('#0f172a')
        ->and($manifest['background_color'])->toBe('#ffffff');

    // Separate icon purpose entries
    expect($manifest['icons'])->toHaveCount(2);
    expect($manifest['icons'][0]['sizes'])->toBe('192x192')
        ->and($manifest['icons'][0]['purpose'])->toBe('any');
    expect($manifest['icons'][1]['sizes'])->toBe('512x512')
        ->and($manifest['icons'][1]['purpose'])->toBe('maskable');
});

test('TC-02: Tenant A users manifest returns student and parent portal payload and scope', function () {
    $response = $this->get('http://acme.test/users/manifest.webmanifest');

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/manifest+json; charset=utf-8');
    $response->assertHeader('Vary', 'Host');

    $manifest = $response->json();

    expect($manifest['id'])->toBe('/users/?tenant=acme')
        ->and($manifest['name'])->toBe('Acme High - Student & Parent Portal')
        ->and($manifest['short_name'])->toBe('Acme High')
        ->and($manifest['start_url'])->toBe('/users/')
        ->and($manifest['scope'])->toBe('/users/')
        ->and($manifest['display'])->toBe('standalone')
        ->and($manifest['theme_color'])->toBe('#4f46e5')
        ->and($manifest['background_color'])->toBe('#ffffff');

    expect($manifest['icons'])->toHaveCount(2);
    expect($manifest['icons'][0]['sizes'])->toBe('192x192')
        ->and($manifest['icons'][0]['purpose'])->toBe('any');
    expect($manifest['icons'][1]['sizes'])->toBe('512x512')
        ->and($manifest['icons'][1]['purpose'])->toBe('maskable');
});

test('TC-03: Side-by-side manifests have distinct IDs, scopes, and names on same tenant domain', function () {
    $staffManifest = $this->get('http://acme.test/staff/manifest.webmanifest')->json();
    $userManifest = $this->get('http://acme.test/users/manifest.webmanifest')->json();

    expect($staffManifest['id'])->not->toBe($userManifest['id'])
        ->and($staffManifest['scope'])->toBe('/staff/')
        ->and($userManifest['scope'])->toBe('/users/')
        ->and($staffManifest['theme_color'])->not->toBe($userManifest['theme_color'])
        ->and($staffManifest['name'])->not->toBe($userManifest['name']);
});

test('TC-04: Tenant B manifest returns isolated branding and assets', function () {
    $response = $this->get('http://globex.test/staff/manifest.webmanifest');

    $response->assertOk();
    $manifest = $response->json();

    expect($manifest['id'])->toBe('/staff/?tenant=globex')
        ->and($manifest['name'])->toBe('Globex Academy - Staff & Teachers')
        ->and($manifest['short_name'])->toBe('Globex Academy Staff')
        ->and($manifest['theme_color'])->toBe('#1e293b')
        ->and($manifest['icons'][0]['src'])->toBe('/icons/globex-staff-192.png');
});

test('Invalid portal in manifest endpoint returns 404', function () {
    $response = $this->get('http://acme.test/admin/manifest.webmanifest');

    $response->assertNotFound();
});

test('TC-07: Root sw.js is served with Service-Worker-Allowed header', function () {
    $response = $this->get('http://acme.test/sw.js');

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/javascript; charset=utf-8');
    $response->assertHeader('Service-Worker-Allowed', '/');
});

test('TC-08: Blade root view renders dynamic manifest link and iOS meta tags', function () {
    $staffPage = $this->get('http://acme.test/staff/login');
    $staffPage->assertOk();
    $staffPage->assertSee('<link rel="manifest" href="http://acme.test/staff/manifest.webmanifest">', false);
    $staffPage->assertSee('<meta name="theme-color" content="#0f172a">', false);
    $staffPage->assertSee('<meta name="apple-mobile-web-app-capable" content="yes">', false);
    $staffPage->assertSee('<meta name="apple-mobile-web-app-title" content="Acme High Staff">', false);

    $userPage = $this->get('http://acme.test/users/login');
    $userPage->assertOk();
    $userPage->assertSee('<link rel="manifest" href="http://acme.test/users/manifest.webmanifest">', false);
    $userPage->assertSee('<meta name="theme-color" content="#4f46e5">', false);
    $userPage->assertSee('<meta name="apple-mobile-web-app-title" content="Acme High">', false);
});
