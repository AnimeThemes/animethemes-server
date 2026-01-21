<?php

declare(strict_types=1);

namespace Database\Seeders\Auth\Prohibition;

use App\Models\Auth\Permission;
use Illuminate\Database\Seeder;
use Kyrch\Prohibition\Models\Prohibition;

class ProhibitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::query()->get([Permission::ATTRIBUTE_NAME])->each(
            function (Permission $permission): void {
                Prohibition::query()->firstOrCreate([
                    'name' => $permission->name,
                ]);
            },
        );
    }
}
