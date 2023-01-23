<?php

declare(strict_types=1);

namespace Database\Seeders\Auth\Role;

use App\Models\Auth\Role;

/**
 * Class PlaylistUserRoleSeeder.
 */
class PlaylistUserRoleSeeder extends RoleSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        /** @var Role $role */
        $role = Role::findOrCreate('Playlist User');

        // List Resources
        $this->configureResource($role, 'playlist', $this->extendedCrudAbilities());
        $this->configureResource($role, 'playlist track', $this->extendedCrudAbilities());

        $role->default = true;
        if ($role->isDirty()) {
            $role->save();
        }
    }
}
