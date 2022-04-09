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
        $this->call(VideoSeeder::class);
        $this->call(AnilistAnimeResourceSeeder::class);
        $this->call(SynopsisCoverSeeder::class);
        $this->call(AniDbResourceSeeder::class);
        $this->call(AnilistArtistResourceSeeder::class);
        $this->call(ArtistCoverSeeder::class);
        $this->call(KitsuResourceSeeder::class);
        $this->call(StudioSeeder::class);
    }
}
