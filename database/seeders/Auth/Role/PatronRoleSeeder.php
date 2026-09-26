<?php

declare(strict_types=1);

namespace Database\Seeders\Auth\Role;

use App\Enums\Auth\Role as RoleEnum;
use App\Enums\Auth\SpecialPermission;
use App\Models\Auth\Role;

class PatronRoleSeeder extends RoleSeeder
{
    /**
     * Run the database seeds.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function run(): void
    {
        $roleEnum = RoleEnum::PATRON;

        /** @var Role $role */
        $role = Role::findOrCreate($roleEnum->value);

        // Special Permissions
        $this->configureAbilities(
            $role,
            [
                SpecialPermission::BYPASS_FEATURE_FLAGS->value,
            ]
        );

        $role->color = $roleEnum->color();
        $role->priority = $roleEnum->priority();

        $role->save();
    }
}
