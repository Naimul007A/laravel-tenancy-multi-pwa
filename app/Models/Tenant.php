<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TenantFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Concerns\HasScopedValidationRules;
use Stancl\Tenancy\Database\Models\Domain;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

/**
 * @property string|null $name
 * @property-read Collection<int, Domain> $domains
 */
class Tenant extends BaseTenant
{
    use HasDomains;

    /** @use HasFactory<TenantFactory> */
    use HasFactory;

    use HasScopedValidationRules;

    /**
     * Get the tenant's primary domain name.
     */
    public function getPrimaryDomain(): ?string
    {
        $domain = $this->domains->first();

        return $domain instanceof Domain
            ? $domain->domain
            : null;
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): TenantFactory
    {
        return TenantFactory::new();
    }
}
