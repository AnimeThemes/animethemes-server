<?php

declare(strict_types=1);

namespace Database\Seeders\Auth\Role;

use App\Models\Auth\Role;

/**
 * Class WikiViewerRoleSeeder.
 */
class WikiViewerRoleSeeder extends RoleSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        /** @var Role $role */
        $role = Role::findOrCreate('Wiki Viewer');

        // List Resources
        $this->configureResource($role, 'playlist', $this->extendedCrudAbilities());
        $this->configureResource($role, 'playlist track', $this->extendedCrudAbilities());

        // Wiki Resources
        $this->configureResource($role, 'anime', ['view']);
        $this->configureResource($role, 'anime synonym', ['view']);
        $this->configureResource($role, 'anime theme', ['view']);
        $this->configureResource($role, 'anime theme entry', ['view']);
        $this->configureResource($role, 'artist', ['view']);
        $this->configureResource($role, 'audio', ['view']);
        $this->configureResource($role, 'external resource', ['view']);
        $this->configureResource($role, 'image', ['view']);
        $this->configureResource($role, 'page', ['view']);
        $this->configureResource($role, 'series', ['view']);
        $this->configureResource($role, 'song', ['view']);
        $this->configureResource($role, 'studio', ['view']);
        $this->configureResource($role, 'video', ['view']);
        $this->configureResource($role, 'video script', ['view']);

        // Special Permissions
        $this->configureAbilities($role, ['view nova']);
    }
}
