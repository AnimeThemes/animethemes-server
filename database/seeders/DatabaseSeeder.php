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
    public function run()
    {
        $this->call(VideoSeeder::class);
        $this->call(VideoTagsSeeder::class);
        $this->call(AnimeSeeder::class);
        $this->call(AnimeResourceSeeder::class);
        $this->call(AnimeSeasonSeeder::class);
        $this->call(MalSeasonYearSeeder::class);
        $this->call(AnimeThemeSeeder::class);
        $this->call(ArtistSeeder::class);
        $this->call(SeriesSeeder::class);
        $this->call(ArtistSongSeeder::class);
        $this->call(AnilistAnimeResourceSeeder::class);
        $this->call(SynopsisCoverSeeder::class);
        $this->call(AniDbResourceSeeder::class);
        $this->call(AnilistArtistResourceSeeder::class);
        $this->call(ArtistCoverSeeder::class);
        $this->call(KitsuResourceSeeder::class);
    }
}
