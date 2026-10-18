<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->baseDomain = config('tenancy.central_domains.0', 'localhost');
    $this->domain = 'acme.'.$this->baseDomain;

    $this->tenant = Tenant::create([
        'id' => 'acme',
        'name' => 'Acme Corporation',
    ]);

    $this->tenant->createDomain(['domain' => $this->domain]);

    $this->user = User::create([
        'name' => 'Acme User',
        'email' => 'user@acme.com',
        'password' => Hash::make('secret-password'),
        'tenant_id' => 'acme',
    ]);
});

test('tenant login page can be rendered on tenant subdomain', function () {
    $response = $this->get("http://{$this->domain}/login");

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('tenant/Login')
            ->where('tenant.id', 'acme')
            ->where('tenant.name', 'Acme Corporation')
            ->where('tenant.domain', $this->domain)
        );
});

test('tenant root route redirects to login for guests', function () {
    $response = $this->get("http://{$this->domain}/");

    $response->assertRedirect("http://{$this->domain}/login");
});

test('tenant user can log in to their tenant workspace', function () {
    $response = $this->post("http://{$this->domain}/login", [
        'email' => 'user@acme.com',
        'password' => 'secret-password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect("http://{$this->domain}/dashboard");
});

test('tenant login fails with invalid credentials', function () {
    $response = $this->post("http://{$this->domain}/login", [
        'email' => 'user@acme.com',
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors('email');
});

test('user from another tenant cannot log into this tenant workspace', function () {
    $otherDomain = 'other.'.$this->baseDomain;
    $otherTenant = Tenant::create(['id' => 'other', 'name' => 'Other Corp']);
    $otherTenant->createDomain(['domain' => $otherDomain]);

    User::create([
        'name' => 'Other User',
        'email' => 'other@corp.com',
        'password' => Hash::make('secret-password'),
        'tenant_id' => 'other',
    ]);

    // Try to log into acme tenant with other tenant user credentials
    $response = $this->post("http://{$this->domain}/login", [
        'email' => 'other@corp.com',
        'password' => 'secret-password',
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors('email');
});

test('unauthenticated user cannot access tenant dashboard', function () {
    $response = $this->get("http://{$this->domain}/dashboard");

    $response->assertRedirect("http://{$this->domain}/login");
});

test('authenticated tenant user can access tenant dashboard', function () {
    $response = $this->actingAs($this->user)
        ->get("http://{$this->domain}/dashboard");

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('tenant/Dashboard')
            ->where('tenant.id', 'acme')
            ->where('tenant.name', 'Acme Corporation')
            ->where('user.email', 'user@acme.com')
        );
});

test('authenticated tenant root route redirects to dashboard', function () {
    $response = $this->actingAs($this->user)
        ->get("http://{$this->domain}/");

    $response->assertRedirect("http://{$this->domain}/dashboard");
});

test('authenticated tenant user can log out', function () {
    $response = $this->actingAs($this->user)
        ->post("http://{$this->domain}/logout");

    $this->assertGuest();
    $response->assertRedirect("http://{$this->domain}/login");
});
