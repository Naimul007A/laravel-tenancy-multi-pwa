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
});

test('staff app install page can be rendered with tenant branding and features', function () {
    $response = $this->get('http://acme.test/staff/app');

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('tenant/staff/App')
            ->where('tenant.id', 'acme')
            ->where('tenant.short_name', 'Acme High')
            ->where('tenant.theme_color', '#0f172a')
            ->has('features')
        );
});

test('users app install page can be rendered with tenant branding and features', function () {
    $response = $this->get('http://acme.test/users/app');

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('tenant/users/App')
            ->where('tenant.id', 'acme')
            ->where('tenant.short_name', 'Acme High')
            ->where('tenant.theme_color', '#4f46e5')
            ->has('features')
        );
});

test('Blade root view injects PWA standalone detection script', function () {
    $response = $this->get('http://acme.test/staff/app');

    $response->assertOk();
    $response->assertSee('// Force PWA Installation: redirect non-standalone web visitors to /{portal}/app', false);
    $response->assertSee('window.location.replace(\'/staff/app\')', false);
    $response->assertSee('window.location.replace(\'/users/app\')', false);
});
