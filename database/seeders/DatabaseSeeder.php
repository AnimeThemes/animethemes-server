<?php

declare(strict_types=1);

namespace Database\Seeders;

use Database\Seeders\Auth\Role\RoleSeeder;
use Database\Seeders\Billing\Transaction\DigitalOceanTransactionSeeder;
use Database\Seeders\Wiki\Audio\AudioSeeder;
use Database\Seeders\Wiki\Video\VideoSeeder;
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
        $this->call(RoleSeeder::class);
        $this->call(VideoSeeder::class);
        $this->call(AudioSeeder::class);
    }
}
