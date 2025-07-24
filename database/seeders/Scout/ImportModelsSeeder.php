<?php

declare(strict_types=1);

namespace Database\Seeders\Scout;

use App\Models\List\Playlist;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Series;
use App\Models\Wiki\Song;
use App\Models\Wiki\Studio;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

class ImportModelsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $driver = Config::get('scout.driver');
        if (empty($driver)) {
            $this->command->info('No driver configured for Scout. Skipping models importing.');

            return;
        }

        $this->scoutImport(Playlist::class);
        $this->scoutImport(Anime::class);
        $this->scoutImport(AnimeSynonym::class);
        $this->scoutImport(AnimeTheme::class);
        $this->scoutImport(AnimeThemeEntry::class);
        $this->scoutImport(Artist::class);
        $this->scoutImport(Series::class);
        $this->scoutImport(Song::class);
        $this->scoutImport(Studio::class);
        $this->scoutImport(Video::class);
    }

    /**
     * Call the scout import command for the given model.
     *
     * @param  class-string<Model>  $modelClass
     */
    private function scoutImport(string $modelClass): void
    {
        $this->command->info("Importing Models for {$modelClass}");
        Artisan::call('scout:import', ['model' => $modelClass]);
    }
}
