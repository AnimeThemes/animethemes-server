<?php

declare(strict_types=1);

namespace App\Console\Commands\Models;

use App\Console\Commands\BaseCommand;
use App\Models\List\Playlist;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Series;
use App\Models\Wiki\Song;
use App\Models\Wiki\Studio;
use App\Models\Wiki\Synonym;
use App\Models\Wiki\Video;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

#[Signature(
    'scout:import-all
    {--flush : Flush and re-import models}'
)]
#[Description('Import the models for Laravel Scout')]
class ImportModelsCommand extends BaseCommand
{
    public function handle(): int
    {
        if (Config::get('app.env') === 'staging') {
            $this->alert('This seeder is not allowed in staging environments.');

            return 0;
        }

        $driver = Config::get('scout.driver');
        if (blank($driver)) {
            $this->error('No driver configured for Scout. Skipping models importing.');

            return 0;
        }

        if ($this->option('flush')) {
            $this->scoutFlush(Playlist::class);
            $this->scoutFlush(Anime::class);
            $this->scoutFlush(AnimeSynonym::class);
            $this->scoutFlush(AnimeTheme::class);
            $this->scoutFlush(AnimeThemeEntry::class);
            $this->scoutFlush(Artist::class);
            $this->scoutFlush(Series::class);
            $this->scoutFlush(Song::class);
            $this->scoutFlush(Studio::class);
            $this->scoutFlush(Synonym::class);
            $this->scoutFlush(Video::class);
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
        $this->scoutImport(Synonym::class);
        $this->scoutImport(Video::class);

        return 1;
    }

    /**
     * Call the scout import command for the given model.
     *
     * @param  class-string<Model>  $modelClass
     */
    private function scoutImport(string $modelClass): void
    {
        $this->info("Importing Models for {$modelClass}");
        $this->call('scout:import', ['model' => $modelClass]);
    }

    /**
     * Call the scout flush command for the given model.
     *
     * @param  class-string<Model>  $modelClass
     */
    private function scoutFlush(string $modelClass): void
    {
        $this->info("Flushing Models for {$modelClass}");
        $this->call('scout:flush', ['model' => $modelClass]);
    }

    protected function validator(): Validator
    {
        return ValidatorFacade::make($this->options(), [
            'flush' => ['boolean'],
        ]);
    }
}
