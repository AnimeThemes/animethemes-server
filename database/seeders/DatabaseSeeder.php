<?php

declare(strict_types=1);

namespace Database\Seeders;

use Database\Seeders\Admin\Feature\FeatureSeeder;
use Database\Seeders\Auth\Permission\PermissionSeeder;
use Database\Seeders\Auth\Role\RoleSeeder;
use Database\Seeders\Scout\ImportModelsSeeder;
use Database\Seeders\Wiki\Audio\AudioSeeder;
use Database\Seeders\Wiki\Video\VideoSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(PermissionSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(VideoSeeder::class);
        $this->call(AudioSeeder::class);
        $this->call(HashidsSeeder::class);
        $this->call(FeatureSeeder::class);
        $this->call(ImportModelsSeeder::class);
    }
}
