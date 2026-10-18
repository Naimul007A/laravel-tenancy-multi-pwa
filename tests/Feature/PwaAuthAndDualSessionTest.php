<?php

declare(strict_types=1);

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->tenant = Tenant::create([
        'id' => 'acme',
        'name' => 'Acme High School',
        'short_name' => 'Acme High',
        'staff_theme_color' => '#0f172a',
        'user_theme_color' => '#4f46e5',
    ]);

    $this->tenant->createDomain(['domain' => 'acme.test']);

    // Teacher who is also a Parent
    $this->dualUser = User::create([
        'name' => 'Jane Teacher-Parent',
        'email' => 'jane@acme.edu',
        'password' => Hash::make('password123'),
        'tenant_id' => 'acme',
        'role' => 'both',
    ]);

    // Staff only user
    $this->staffOnlyUser = User::create([
        'name' => 'Bob Teacher',
        'email' => 'bob@acme.edu',
        'password' => Hash::make('password123'),
        'tenant_id' => 'acme',
        'role' => 'staff',
    ]);

    // Parent/Student only user
    $this->userOnlyUser = User::create([
        'name' => 'Alice Parent',
        'email' => 'alice@gmail.com',
        'password' => Hash::make('password123'),
        'tenant_id' => 'acme',
        'role' => 'user',
    ]);
});

test('TC-05: Unauthenticated access to /staff/dashboard redirects to /staff/login', function () {
    $response = $this->get('http://acme.test/staff/dashboard');

    $response->assertRedirect('http://acme.test/staff/login');
});

test('TC-05: Unauthenticated access to /users/dashboard redirects to /users/login', function () {
    $response = $this->get('http://acme.test/users/dashboard');

    $response->assertRedirect('http://acme.test/users/login');
});

test('TC-06: Dual session coexistence - Staff logout does not terminate Student/Parent session', function () {
    // 1. Log in to Staff Portal
    $staffLoginResponse = $this->post('http://acme.test/staff/login', [
        'email' => 'jane@acme.edu',
        'password' => 'password123',
    ]);

    $staffLoginResponse->assertRedirect('http://acme.test/staff/dashboard');
    expect(auth()->guard('staff')->check())->toBeTrue();

    // 2. Log in to Users/Parent Portal
    $userLoginResponse = $this->post('http://acme.test/users/login', [
        'email' => 'jane@acme.edu',
        'password' => 'password123',
    ]);

    $userLoginResponse->assertRedirect('http://acme.test/users/dashboard');
    expect(auth()->guard('web')->check())->toBeTrue()
        ->and(auth()->guard('staff')->check())->toBeTrue();

    // Verify both dashboards are accessible
    $this->get('http://acme.test/staff/dashboard')->assertOk();
    $this->get('http://acme.test/users/dashboard')->assertOk();

    // 3. Log out of Staff Portal
    $staffLogoutResponse = $this->post('http://acme.test/staff/logout');
    $staffLogoutResponse->assertRedirect('http://acme.test/staff/login');

    expect(auth()->guard('staff')->check())->toBeFalse()
        ->and(auth()->guard('web')->check())->toBeTrue();

    // 4. Verify User Portal session is still active and accessible
    $this->get('http://acme.test/users/dashboard')->assertOk();

    // 5. Staff dashboard now redirects to staff login
    $this->get('http://acme.test/staff/dashboard')->assertRedirect('http://acme.test/staff/login');
});

test('Staff-only user cannot log in to users portal', function () {
    $response = $this->from('http://acme.test/users/login')->post('http://acme.test/users/login', [
        'email' => 'bob@acme.edu',
        'password' => 'password123',
    ]);

    $response->assertSessionHasErrors('email');
    expect(auth()->guard('web')->check())->toBeFalse();
});

test('User-only user cannot log in to staff portal', function () {
    $response = $this->from('http://acme.test/staff/login')->post('http://acme.test/staff/login', [
        'email' => 'alice@gmail.com',
        'password' => 'password123',
    ]);

    $response->assertSessionHasErrors('email');
    expect(auth()->guard('staff')->check())->toBeFalse();
});
