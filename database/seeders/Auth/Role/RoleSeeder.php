<?php

declare(strict_types=1);

namespace Database\Seeders\Auth\Role;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\Permission;
use App\Models\Auth\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(AdminSeeder::class);
        $this->call(ContentModeratorRoleSeeder::class);
        $this->call(ContributorRoleSeeder::class);
        $this->call(DeveloperRoleSeeder::class);
        $this->call(EncoderRoleSeeder::class);
        $this->call(PanelViewerRoleSeeder::class);
        $this->call(PatronRoleSeeder::class);
        $this->call(VerifiedRoleSeeder::class);
    }

    /**
     * Configure role with resource abilities.
     *
     * @param  array<int, CrudPermission|ExtendedCrudPermission>  $abilities
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
     * @param  string[]  $abilities
     */
    protected function configureAbilities(Role $role, array $abilities): void
    {
        $permissions = Arr::map($abilities, fn (string $ability) => Permission::findByName($ability));

        $role->givePermissionTo($permissions);
    }
}
