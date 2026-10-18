<?php

use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::registration());
});

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new users can register', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('new users can register and create a tenant with subdomain', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Acme Admin',
        'email' => 'admin@acme.com',
        'subdomain' => 'acme-corp',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $this->assertDatabaseHas('tenants', [
        'id' => 'acme-corp',
    ]);
    $centralDomain = (array) config('tenancy.central_domains', ['localhost']);
    $baseDomain = $centralDomain[0] ?? 'localhost';

    $this->assertDatabaseHas('domains', [
        'domain' => 'acme-corp.'.$baseDomain,
        'tenant_id' => 'acme-corp',
    ]);
    $this->assertDatabaseHas('users', [
        'email' => 'admin@acme.com',
        'tenant_id' => 'acme-corp',
    ]);
});
