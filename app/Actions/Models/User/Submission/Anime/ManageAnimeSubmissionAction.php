<?php

declare(strict_types=1);

namespace App\Actions\Models\User\Submission\Anime;

use App\Actions\Models\User\Submission\SubmissionAction;
use App\Concerns\Models\CanCreateSynonym;
use App\Filament\Resources\Wiki\Song\RelationManagers\PerformanceSongRelationManager;
use App\Models\User\Submission\SubmissionStage;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ManageAnimeSubmissionAction extends SubmissionAction
{
    use CanCreateSynonym;

    /**
     * Approve the submission stage.
     */
    public function approve(SubmissionStage $stage): void
    {
        DB::beginTransaction();

        $fields = $stage->getAttribute(SubmissionStage::ATTRIBUTE_FIELDS);

        $fields = $this->createVirtuals($fields);

        $anime = Anime::query()->create(Arr::array($fields, 'anime'));

        $this->createSynonyms(Arr::array($fields, 'animesynonyms'), $anime);

        $this->createThemes(Arr::array($fields, 'animethemes'), $anime);

        $this->syncSeries(Arr::array($fields, 'series'), $anime);

        $this->syncResources(Arr::array($fields, 'resources'), $anime);

        $this->syncStudios(Arr::array($fields, 'studios'), $anime);

        DB::commit();
    }

    protected function createSynonyms(array $synonyms, Anime $anime): void
    {
        foreach ($synonyms as $synonym) {
            $this->createSynonym(
                Arr::get($synonym, 'text'),
                Arr::get($synonym, 'type'),
                $anime
            );
        }
    }

    protected function createThemes(array $themes, Anime $anime): void
    {
        foreach ($themes as $theme) {
            // TODO: Convert to DTO
            $themeModel = AnimeTheme::query()->create([
                AnimeTheme::ATTRIBUTE_ANIME => $anime->getKey(),
                ...Arr::only($theme, new AnimeTheme()->getFillable()),
            ]);

            PerformanceSongRelationManager::saveArtists(Arr::get($theme, 'song_id'), Arr::get($theme, 'performances'));

            foreach ($theme['animethemeentries'] as $entry) {
                AnimeThemeEntry::query()->create([
                    AnimeThemeEntry::ATTRIBUTE_THEME => $themeModel->getKey(),
                    ...Arr::only($entry, new AnimeThemeEntry()->getFillable()),
                ]);
            }
        }
    }

    protected function syncSeries(array $series, Anime $anime): void
    {
        $seriesToSync = [];

        foreach ($series as $series) {
            $seriesToSync[Arr::integer($series, 'series_id')] = Arr::except($series, 'series_id');
        }

        $anime->series()->sync($seriesToSync);
    }

    protected function syncResources(array $resources, Anime $anime): void
    {
        $resourcesToSync = [];

        foreach ($resources as $resource) {
            $resourcesToSync[Arr::integer($resource, 'resource_id')] = Arr::except($resource, 'resource_id');
        }

        $anime->resources()->sync($resourcesToSync);
    }

    protected function syncStudios(array $studios, Anime $anime): void
    {
        $studiosToSync = [];

        foreach ($studios as $studio) {
            $studiosToSync[Arr::integer($studio, 'studio_id')] = Arr::except($studio, 'studio_id');
        }

        $anime->studios()->sync($studiosToSync);
    }
}
