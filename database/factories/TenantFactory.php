<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tenant>
 */
class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->company();

        return [
            'id' => (string) Str::uuid(),
            'name' => $name,
            'short_name' => Str::limit($name, 25, ''),
            'staff_theme_color' => '#0f172a',
            'user_theme_color' => '#4f46e5',
            'staff_icon_192' => '/icons/staff-192x192.png',
            'staff_icon_512' => '/icons/staff-512x512.png',
            'user_icon_192' => '/icons/user-192x192.png',
            'user_icon_512' => '/icons/user-512x512.png',
            'data' => [],
        ];
    }
}
