<?php

declare(strict_types=1);

namespace Database\Seeders\Auth\Role;

use App\Models\Auth\Role;

/**
 * Class AdminSeeder.
 */
class AdminSeeder extends RoleSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        /** @var Role $role */
        $role = Role::findOrCreate('Admin');

        // Admin Resources
        $this->configureResource($role, 'announcement', $this->extendedCrudAbilities());
        $this->configureResource($role, 'dump', $this->extendedCrudAbilities());
        $this->configureResource($role, 'setting', $this->crudAbilities());

        // Auth Resources
        $this->configureResource($role, 'permission', ['view']);
        $this->configureResource($role, 'role', $this->crudAbilities());
        $this->configureResource($role, 'user', $this->extendedCrudAbilities());

        // Billing Resources
        $this->configureResource($role, 'balance', $this->extendedCrudAbilities());
        $this->configureResource($role, 'transaction', $this->extendedCrudAbilities());

        // List Resources
        $this->configureResource($role, 'playlist', $this->extendedCrudAbilities());
        $this->configureResource($role, 'playlist track', $this->extendedCrudAbilities());

        // Wiki Resources
        $this->configureResource($role, 'anime', $this->extendedCrudAbilities());
        $this->configureResource($role, 'anime synonym', $this->extendedCrudAbilities());
        $this->configureResource($role, 'anime theme', $this->extendedCrudAbilities());
        $this->configureResource($role, 'anime theme entry', $this->extendedCrudAbilities());
        $this->configureResource($role, 'artist', $this->extendedCrudAbilities());
        $this->configureResource($role, 'audio', $this->extendedCrudAbilities());
        $this->configureResource($role, 'external resource', $this->extendedCrudAbilities());
        $this->configureResource($role, 'image', $this->extendedCrudAbilities());
        $this->configureResource($role, 'page', $this->extendedCrudAbilities());
        $this->configureResource($role, 'series', $this->extendedCrudAbilities());
        $this->configureResource($role, 'song', $this->extendedCrudAbilities());
        $this->configureResource($role, 'studio', $this->extendedCrudAbilities());
        $this->configureResource($role, 'video', $this->extendedCrudAbilities());
        $this->configureResource($role, 'video script', $this->extendedCrudAbilities());

        // Special Permissions
        $this->configureAbilities($role, ['view nova', 'view telescope', 'view horizon', 'bypass api rate limiter']);
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
}
