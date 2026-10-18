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

test('staff login page can be rendered on tenant subdomain', function () {
    $response = $this->get("http://{$this->domain}/staff/login");

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('tenant/staff/Login')
            ->where('tenant.id', 'acme')
        );
});

test('users login page can be rendered on tenant subdomain', function () {
    $response = $this->get("http://{$this->domain}/users/login");

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('tenant/users/Login')
            ->where('tenant.id', 'acme')
        );
});

test('tenant root route redirects to staff login for guests', function () {
    $response = $this->get("http://{$this->domain}/");

    $response->assertRedirect("http://{$this->domain}/staff/login");
});

test('staff user can log in to their staff workspace', function () {
    $response = $this->post("http://{$this->domain}/staff/login", [
        'email' => 'user@acme.com',
        'password' => 'secret-password',
    ]);

    expect(auth()->guard('staff')->check())->toBeTrue();
    $response->assertRedirect("http://{$this->domain}/staff/dashboard");
});

test('staff login fails with invalid credentials', function () {
    $response = $this->from("http://{$this->domain}/staff/login")->post("http://{$this->domain}/staff/login", [
        'email' => 'user@acme.com',
        'password' => 'wrong-password',
    ]);

    expect(auth()->guard('staff')->check())->toBeFalse();
    $response->assertSessionHasErrors('email');
});

test('user from another tenant cannot log into this tenant staff portal', function () {
    $otherDomain = 'other.'.$this->baseDomain;
    $otherTenant = Tenant::create(['id' => 'other', 'name' => 'Other Corp']);
    $otherTenant->createDomain(['domain' => $otherDomain]);

    User::create([
        'name' => 'Other User',
        'email' => 'other@corp.com',
        'password' => Hash::make('secret-password'),
        'tenant_id' => 'other',
        'role' => 'both',
    ]);

    // Try to log into acme tenant with other tenant user credentials
    $response = $this->from("http://{$this->domain}/staff/login")->post("http://{$this->domain}/staff/login", [
        'email' => 'other@corp.com',
        'password' => 'secret-password',
    ]);

    expect(auth()->guard('staff')->check())->toBeFalse();
    $response->assertSessionHasErrors('email');
});

test('unauthenticated user cannot access staff dashboard', function () {
    $response = $this->get("http://{$this->domain}/staff/dashboard");

    $response->assertRedirect("http://{$this->domain}/staff/login");
});

test('authenticated staff user can access staff dashboard', function () {
    $response = $this->actingAs($this->user, 'staff')
        ->get("http://{$this->domain}/staff/dashboard");

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('tenant/staff/Dashboard')
            ->where('tenant.id', 'acme')
            ->where('user.email', 'user@acme.com')
        );
});

test('authenticated staff user can log out', function () {
    $response = $this->actingAs($this->user, 'staff')
        ->post("http://{$this->domain}/staff/logout");

    expect(auth()->guard('staff')->check())->toBeFalse();
    $response->assertRedirect("http://{$this->domain}/staff/login");
});
