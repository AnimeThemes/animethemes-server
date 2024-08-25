<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Auth\Permission;
use Illuminate\Database\Seeder;

/**
 * Class DeletePermissionsSeeder.
 */
class DeletePermissionsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        Permission::query()
            ->orWhere(Permission::ATTRIBUTE_NAME, 'create external entry')
            ->orWhere(Permission::ATTRIBUTE_NAME, 'update external entry')
            ->orWhere(Permission::ATTRIBUTE_NAME, 'delete external entry')
            ->orWhere(Permission::ATTRIBUTE_NAME, 'force delete external entry')
            ->orWhere(Permission::ATTRIBUTE_NAME, 'restore external entry')
            ->delete();
    }
}
