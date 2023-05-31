<?php

declare(strict_types=1);

namespace Database\Seeders\Auth\Role;

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\Role;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;

/**
 * Class PlaylistUserRoleSeeder.
 */
class PlaylistUserRoleSeeder extends RoleSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function run(): void
    {
        /** @var Role $role */
        $role = Role::findOrCreate('Playlist User');

        // List Resources
        $this->configureResource($role, Playlist::class, ExtendedCrudPermission::getInstances());
        $this->configureResource($role, PlaylistTrack::class, ExtendedCrudPermission::getInstances());

        $role->default = true;

        $role->save();
    }
}
