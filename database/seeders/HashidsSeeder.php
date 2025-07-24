<?php

declare(strict_types=1);

namespace Database\Seeders;

use Database\Seeders\List\Playlist\PlaylistHashidsSeeder;
use Database\Seeders\List\Playlist\Track\TrackHashidsSeeder;
use Illuminate\Database\Seeder;

class HashidsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(PlaylistHashidsSeeder::class);
        $this->call(TrackHashidsSeeder::class);
    }
}
