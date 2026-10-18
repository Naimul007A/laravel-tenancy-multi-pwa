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
 * @property string $id
 * @property string|null $short_name
 * @property string|null $staff_theme_color
 * @property string|null $user_theme_color
 * @property string|null $staff_icon_192
 * @property string|null $staff_icon_512
 * @property string|null $user_icon_192
 * @property string|null $user_icon_512
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

    public function getShortName(): string
    {
        return $this->short_name ?: ($this->name ?: (string) $this->id);
    }

    public function getStaffThemeColor(): string
    {
        return $this->staff_theme_color ?: '#0f172a';
    }

    public function getUserThemeColor(): string
    {
        return $this->user_theme_color ?: '#4f46e5';
    }

    public function getStaffIcon192(): string
    {
        return $this->staff_icon_192 ?: '/icons/staff-192x192.png';
    }

    public function getStaffIcon512(): string
    {
        return $this->staff_icon_512 ?: '/icons/staff-512x512.png';
    }

    public function getUserIcon192(): string
    {
        return $this->user_icon_192 ?: '/icons/user-192x192.png';
    }

    public function getUserIcon512(): string
    {
        return $this->user_icon_512 ?: '/icons/user-512x512.png';
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): TenantFactory
    {
        return TenantFactory::new();
    }
}
