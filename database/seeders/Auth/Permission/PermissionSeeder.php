<?php

declare(strict_types=1);

namespace Database\Seeders\Auth\Permission;

use App\Models\Auth\Permission;
use Illuminate\Database\Seeder;

/**
 * Class PermissionSeeder.
 */
class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $this->registerResource('announcement', $this->extendedCrudAbilities());
        $this->registerResource('dump', $this->extendedCrudAbilities());
        $this->registerResource('setting', $this->crudAbilities());

        // Auth Resources
        $this->registerResource('permission', ['view']);
        $this->registerResource('role', $this->crudAbilities());
        $this->registerResource('user', $this->extendedCrudAbilities());

        // Billing Resources
        $this->registerResource('balance', $this->extendedCrudAbilities());
        $this->registerResource('transaction', $this->extendedCrudAbilities());

        // List Resources
        $this->registerResource('playlist', $this->extendedCrudAbilities());
        $this->registerResource('playlist track', $this->extendedCrudAbilities());

        // Wiki Resources
        $this->registerResource('anime', $this->extendedCrudAbilities());
        $this->registerResource('anime synonym', $this->extendedCrudAbilities());
        $this->registerResource('anime theme', $this->extendedCrudAbilities());
        $this->registerResource('anime theme entry', $this->extendedCrudAbilities());
        $this->registerResource('artist', $this->extendedCrudAbilities());
        $this->registerResource('audio', $this->extendedCrudAbilities());
        $this->registerResource('external resource', $this->extendedCrudAbilities());
        $this->registerResource('image', $this->extendedCrudAbilities());
        $this->registerResource('page', $this->extendedCrudAbilities());
        $this->registerResource('series', $this->extendedCrudAbilities());
        $this->registerResource('song', $this->extendedCrudAbilities());
        $this->registerResource('studio', $this->extendedCrudAbilities());
        $this->registerResource('video', $this->extendedCrudAbilities());
        $this->registerResource('video script', $this->extendedCrudAbilities());

        // Special Permissions
        $this->registerAbilities([
            'view nova',
            'view telescope',
            'view horizon',
            'bypass api rate limiter',
        ]);
    }

    /**
     * Get CRUD abilities for resource.
     *
     * @return array<int, string>
     */
    protected function crudAbilities(): array
    {
        return [
            'view',
            'create',
            'update',
            'delete',
        ];
    }

    /**
     * Get extended CRUD abilities for soft-delete resource.
     *
     * @return array<int, string>
     */
    protected function extendedCrudAbilities(): array
    {
        return array_merge(
            $this->crudAbilities(),
            [
                'restore',
                'force delete',
            ],
        );
    }

    /**
     * Register resource abilities.
     *
     * @param  string  $resource
     * @param  array<int, string>  $abilities
     * @return void
     */
    protected function registerResource(string $resource, array $abilities): void
    {
        foreach ($abilities as $ability) {
            Permission::findOrCreate("$ability $resource");
        }
    }

    /**
     * Configure role with abilities.
     *
     * @param  array<int, string>  $abilities
     * @return void
     */
    protected function registerAbilities(array $abilities): void
    {
        foreach ($abilities as $ability) {
            Permission::findOrCreate($ability);
        }
    }
}
