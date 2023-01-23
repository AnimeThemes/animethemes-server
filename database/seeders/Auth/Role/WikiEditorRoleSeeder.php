<?php

declare(strict_types=1);

namespace Database\Seeders\Auth\Role;

use App\Models\Auth\Role;

/**
 * Class WikiEditorRoleSeeder.
 */
class WikiEditorRoleSeeder extends RoleSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        /** @var Role $role */
        $role = Role::findOrCreate('Wiki Editor');

        // List Resources
        $this->configureResource($role, 'playlist', $this->extendedCrudAbilities());
        $this->configureResource($role, 'playlist track', $this->extendedCrudAbilities());

        // Wiki Resources
        $this->configureResource($role, 'anime', $this->extendedCrudAbilities());
        $this->configureResource($role, 'anime synonym', $this->extendedCrudAbilities());
        $this->configureResource($role, 'anime theme', $this->extendedCrudAbilities());
        $this->configureResource($role, 'anime theme entry', $this->extendedCrudAbilities());
        $this->configureResource($role, 'artist', $this->extendedCrudAbilities());
        $this->configureResource($role, 'audio', ['view', 'update']);
        $this->configureResource($role, 'external resource', $this->extendedCrudAbilities());
        $this->configureResource($role, 'image', $this->extendedCrudAbilities());
        $this->configureResource($role, 'page', $this->extendedCrudAbilities());
        $this->configureResource($role, 'series', $this->extendedCrudAbilities());
        $this->configureResource($role, 'song', $this->extendedCrudAbilities());
        $this->configureResource($role, 'studio', $this->extendedCrudAbilities());
        $this->configureResource($role, 'video', ['view', 'update']);
        $this->configureResource($role, 'video script', ['view']);

        // Special Permissions
        $this->configureAbilities($role, ['view nova']);
    }
}
