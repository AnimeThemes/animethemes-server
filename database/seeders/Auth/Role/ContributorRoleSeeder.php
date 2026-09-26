<?php

declare(strict_types=1);

namespace Database\Seeders\Auth\Role;

use App\Enums\Auth\Role as RoleEnum;
use App\Models\Auth\Role;

class ContributorRoleSeeder extends RoleSeeder
{
    /**
     * Run the database seeds.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function run(): void
    {
        $roleEnum = RoleEnum::CONTRIBUTOR;

        /** @var Role $role */
        $role = Role::findOrCreate($roleEnum->value);

        $role->color = $roleEnum->color();
        $role->priority = $roleEnum->priority();

        $role->save();
    }
}
