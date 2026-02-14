<?php

declare(strict_types=1);

namespace Database\Seeders\Auth\Role;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\Role as RoleEnum;
use App\Models\Auth\Role;
use App\Models\List\External\ExternalEntry;
use App\Models\List\ExternalProfile;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\User\Like;
use App\Models\User\Notification;
use App\Models\User\WatchHistory;

class VerifiedRoleSeeder extends RoleSeeder
{
    /**
     * Run the database seeds.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function run(): void
    {
        $roleEnum = RoleEnum::VERIFIED;

        /** @var Role $role */
        $role = Role::findOrCreate($roleEnum->value);

        // User Resources
        $this->configureResource($role, Like::class, CrudPermission::cases());
        $this->configureResource($role, Notification::class, CrudPermission::cases());
        $this->configureResource($role, WatchHistory::class, CrudPermission::cases());

        // List Resources
        $this->configureResource($role, ExternalEntry::class, [CrudPermission::VIEW]);
        $this->configureResource($role, ExternalProfile::class, CrudPermission::cases());
        $this->configureResource($role, Playlist::class, CrudPermission::cases());
        $this->configureResource($role, PlaylistTrack::class, CrudPermission::cases());

        $role->color = $roleEnum->color();
        $role->priority = $roleEnum->priority();
        $role->default = true;

        $role->save();
    }
}
