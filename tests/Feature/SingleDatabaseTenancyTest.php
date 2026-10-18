<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Stancl\Tenancy\Database\Concerns\BelongsToPrimaryModel;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class TestPost extends Model
{
    use BelongsToTenant;

    protected $table = 'test_posts';

    protected $guarded = [];

    public function comments()
    {
        return $this->hasMany(TestComment::class, 'post_id');
    }
}

class TestComment extends Model
{
    use BelongsToPrimaryModel;

    protected $table = 'test_comments';

    protected $guarded = [];

    public function getRelationshipToPrimaryModel(): string
    {
        return 'post';
    }

    public function post()
    {
        return $this->belongsTo(TestPost::class, 'post_id');
    }
}

beforeEach(function () {
    Schema::create('test_posts', function (Blueprint $table) {
        $table->id();
        $table->string('tenant_id');
        $table->string('title');
        $table->string('slug');
        $table->timestamps();

        $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
    });

    Schema::create('test_comments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('post_id')->constrained('test_posts')->cascadeOnDelete();
        $table->string('body');
        $table->timestamps();
    });
});

afterEach(function () {
    Schema::dropIfExists('test_comments');
    Schema::dropIfExists('test_posts');
});

test('tenant can be created without triggering separate database creation in single db mode', function () {
    $tenant = Tenant::create([
        'id' => 'acme',
        'name' => 'Acme Corp',
    ]);

    expect($tenant)->toBeInstanceOf(Tenant::class)
        ->and($tenant->id)->toBe('acme')
        ->and($tenant->name)->toBe('Acme Corp');

    $this->assertDatabaseHas('tenants', [
        'id' => 'acme',
    ]);
});

test('tenant route can be resolved via tenant domain', function () {
    $baseDomain = config('tenancy.central_domains.0', 'localhost');
    $tenant = Tenant::create(['id' => 'alpha']);
    $tenant->createDomain(['domain' => 'alpha.'.$baseDomain]);

    $response = $this->get('http://alpha.'.$baseDomain.'/');

    $response->assertRedirect('http://alpha.'.$baseDomain.'/staff/login');
});

test('prevent access to tenant routes from central domains', function () {
    $baseDomain = config('tenancy.central_domains.0', 'localhost');
    $response = $this->get('http://'.$baseDomain.'/');

    // Central domain hits the welcome inertia page, not the tenant route
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('Welcome'));
});

test('primary models with BelongsToTenant automatically assign tenant_id and scope queries', function () {
    $tenant1 = Tenant::create(['id' => 'tenant-1']);
    $tenant2 = Tenant::create(['id' => 'tenant-2']);

    // Inside tenant 1 context
    tenancy()->initialize($tenant1);

    $post1 = TestPost::create([
        'title' => 'Tenant 1 Post',
        'slug' => 'tenant-1-post',
    ]);

    expect($post1->tenant_id)->toBe('tenant-1')
        ->and(TestPost::count())->toBe(1);

    tenancy()->end();

    // Inside tenant 2 context
    tenancy()->initialize($tenant2);

    expect(TestPost::count())->toBe(0);

    $post2 = TestPost::create([
        'title' => 'Tenant 2 Post',
        'slug' => 'tenant-2-post',
    ]);

    expect($post2->tenant_id)->toBe('tenant-2')
        ->and(TestPost::count())->toBe(1)
        ->and(TestPost::first()->title)->toBe('Tenant 2 Post');

    tenancy()->end();

    // Central context (without tenant) can query all records with withoutTenancy()
    expect(TestPost::withoutTenancy()->count())->toBe(2);
});

test('secondary models with BelongsToPrimaryModel are scoped to current tenant', function () {
    $tenant1 = Tenant::create(['id' => 't1']);
    $tenant2 = Tenant::create(['id' => 't2']);

    // Create post & comment for tenant 1
    tenancy()->initialize($tenant1);
    $post1 = TestPost::create(['title' => 'P1', 'slug' => 'p1']);
    $comment1 = $post1->comments()->create(['body' => 'Comment for P1']);
    expect(TestComment::count())->toBe(1);
    tenancy()->end();

    // Create post & comment for tenant 2
    tenancy()->initialize($tenant2);
    $post2 = TestPost::create(['title' => 'P2', 'slug' => 'p2']);
    $comment2 = $post2->comments()->create(['body' => 'Comment for P2']);
    expect(TestComment::count())->toBe(1);
    expect(TestComment::first()->body)->toBe('Comment for P2');
    tenancy()->end();
});

test('tenant scoped validation rules unique and exists work properly in single db mode', function () {
    $tenant1 = Tenant::create(['id' => 'company-a']);
    $tenant2 = Tenant::create(['id' => 'company-b']);

    tenancy()->initialize($tenant1);
    TestPost::create(['title' => 'Welcome', 'slug' => 'welcome']);

    // For tenant 1, 'welcome' slug already exists
    $validator1 = Validator::make(
        ['slug' => 'welcome'],
        ['slug' => [$tenant1->unique('test_posts', 'slug')]]
    );
    expect($validator1->fails())->toBeTrue();

    // 'slug-new' is available
    $validatorAvailable = Validator::make(
        ['slug' => 'slug-new'],
        ['slug' => [$tenant1->unique('test_posts', 'slug')]]
    );
    expect($validatorAvailable->passes())->toBeTrue();

    // Exists rule for tenant 1
    $validatorExists = Validator::make(
        ['slug' => 'welcome'],
        ['slug' => [$tenant1->exists('test_posts', 'slug')]]
    );
    expect($validatorExists->passes())->toBeTrue();

    tenancy()->end();

    // For tenant 2, 'welcome' slug should be allowed (same slug across different tenants)
    tenancy()->initialize($tenant2);

    $validator2 = Validator::make(
        ['slug' => 'welcome'],
        ['slug' => [$tenant2->unique('test_posts', 'slug')]]
    );
    expect($validator2->passes())->toBeTrue();

    // But exists rule for tenant 2 should fail since tenant 2 has no 'welcome' post
    $validator2Exists = Validator::make(
        ['slug' => 'welcome'],
        ['slug' => [$tenant2->exists('test_posts', 'slug')]]
    );
    expect($validator2Exists->fails())->toBeTrue();

    tenancy()->end();
});

test('tenant can be deleted without attempting to delete a database', function () {
    $tenant = Tenant::create(['id' => 'to-delete']);

    $this->assertDatabaseHas('tenants', ['id' => 'to-delete']);

    $tenant->delete();

    $this->assertDatabaseMissing('tenants', ['id' => 'to-delete']);
});
