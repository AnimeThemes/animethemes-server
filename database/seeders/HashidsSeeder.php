<?php

declare(strict_types=1);

namespace Database\Seeders;

use Database\Seeders\List\Playlist\PlaylistHashidsSeeder;
use Database\Seeders\List\Playlist\Track\TrackHashidsSeeder;
use Illuminate\Database\Seeder;

/**
 * Class HashidsSeeder.
 */
class HashidsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call(PlaylistHashidsSeeder::class);
        $this->call(TrackHashidsSeeder::class);
    }
}
