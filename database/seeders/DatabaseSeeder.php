<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Class DatabaseSeeder.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call(DigitalOceanTransactionSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(VideoSeeder::class);
    }
}
