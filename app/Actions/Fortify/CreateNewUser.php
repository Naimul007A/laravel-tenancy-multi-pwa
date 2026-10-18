<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user and their tenant workspace.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
            'subdomain' => [
                'nullable',
                'string',
                'alpha_dash',
                'min:3',
                'max:50',
                'lowercase',
                'unique:tenants,id',
                Rule::notIn(['www', 'admin', 'api', 'central', 'localhost', 'mail', 'app']),
            ],
        ])->validate();

        return DB::transaction(function () use ($input) {
            $subdomain = ! empty($input['subdomain'])
                ? Str::lower($input['subdomain'])
                : $this->generateSubdomain($input['name']);

            $companyName = $input['company_name'] ?? ($input['name']."'s Workspace");

            $tenant = Tenant::create([
                'id' => $subdomain,
                'name' => $companyName,
                'short_name' => Str::limit($companyName, 30, ''),
                'staff_theme_color' => '#0f172a',
                'user_theme_color' => '#4f46e5',
                'staff_icon_192' => '/icons/staff-192x192.png',
                'staff_icon_512' => '/icons/staff-512x512.png',
                'user_icon_192' => '/icons/user-192x192.png',
                'user_icon_512' => '/icons/user-512x512.png',
            ]);

            $host = request()->getHost();
            $centralDomains = (array) config('tenancy.central_domains', ['localhost']);
            $baseDomain = in_array($host, $centralDomains, true)
                ? $host
                : ($centralDomains[0] ?? 'localhost');

            $tenantDomain = $subdomain.'.'.$baseDomain;
            $tenant->createDomain(['domain' => $tenantDomain]);

            return User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => $input['password'],
                'tenant_id' => $tenant->id,
                'role' => 'both',
            ]);
        });
    }

    /**
     * Generate a unique fallback subdomain based on user name.
     */
    protected function generateSubdomain(string $name): string
    {
        $base = Str::slug($name);
        if (strlen($base) < 3) {
            $base = 'team-'.$base;
        }

        $subdomain = $base;
        $counter = 1;
        while (Tenant::where('id', $subdomain)->exists()) {
            $subdomain = $base.'-'.$counter;
            $counter++;
        }

        return $subdomain;
    }
}
