<?php

declare(strict_types=1);

namespace Database\Seeders\Auth\Role;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\Permission;
use App\Models\Auth\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

/**
 * Class RoleSeeder.
 */
class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call(AdminSeeder::class);
        $this->call(WikiEditorRoleSeeder::class);
        $this->call(WikiViewerRoleSeeder::class);
        $this->call(PlaylistUserRoleSeeder::class);
        $this->call(PatronRoleSeeder::class);
    }

    /**
     * Configure role with resource abilities.
     *
     * @param  Role  $role
     * @param  string  $resource
     * @param  array  $abilities
     * @return void
     */
    protected function configureResource(Role $role, string $resource, array $abilities): void
    {
        $permissions = Arr::map(
            $abilities,
            fn (CrudPermission|ExtendedCrudPermission $ability) => Permission::findByName($ability->format($resource))
        );

        $role->givePermissionTo($permissions);
    }

    /**
     * Configure role with abilities.
     *
     * @param  Role  $role
     * @param  array<int, string>  $abilities
     * @return void
     */
    protected function configureAbilities(Role $role, array $abilities): void
    {
        $permissions = Arr::map($abilities, fn (string $ability) => Permission::findByName($ability));

        $role->givePermissionTo($permissions);
    }
}
