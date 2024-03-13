<?php

declare(strict_types=1);

namespace Database\Seeders\Auth\Role;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\Role;
use App\Models\List\External\ExternalEntry;
use App\Models\List\ExternalProfile;
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

        $extendedCrudPermissions = array_merge(
            CrudPermission::cases(),
            ExtendedCrudPermission::cases(),
        );

        // List Resources
        $this->configureResource($role, ExternalEntry::class, $extendedCrudPermissions);
        $this->configureResource($role, ExternalProfile::class, $extendedCrudPermissions);
        $this->configureResource($role, Playlist::class, $extendedCrudPermissions);
        $this->configureResource($role, PlaylistTrack::class, $extendedCrudPermissions);

        $role->default = true;

        $role->save();
    }
}
