<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime;

use App\Contracts\Events\CascadesRestoresEvent;
use App\Events\Base\Wiki\WikiRestoredEvent;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\SoftDeletingScope;

/**
 * Class AnimeRestored.
 *
 * @extends WikiRestoredEvent<Anime>
 */
class AnimeRestored extends WikiRestoredEvent implements CascadesRestoresEvent
{
    /**
     * Create a new event instance.
     *
     * @param  Anime  $anime
     */
    public function __construct(Anime $anime)
    {
        parent::__construct($anime);
    }

    /**
     * Get the model that has fired this event.
     *
     * @return Anime
     */
    public function getModel(): Anime
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     *
     * @return string
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Anime '**{$this->getModel()->getName()}**' has been restored.";
    }

    /**
     * Perform cascading restores.
     *
     * @return void
     */
    public function cascadeRestores(): void
    {
        $anime = $this->getModel();

        $anime->animesynonyms()->withoutGlobalScope(SoftDeletingScope::class)->get()->each(function (AnimeSynonym $synonym) {
            AnimeSynonym::withoutEvents(function () use ($synonym) {
                $synonym->restore();
                $synonym->searchable();
            });
        });

        $anime->animethemes()->withoutGlobalScope(SoftDeletingScope::class)->get()->each(function (AnimeTheme $theme) {
            AnimeTheme::withoutEvents(function () use ($theme) {
                $theme->restore();
                $theme->searchable();
                $theme->animethemeentries()->withoutGlobalScope(SoftDeletingScope::class)->get()->each(function (AnimeThemeEntry $entry) {
                    AnimeThemeEntry::withoutEvents(function () use ($entry) {
                        $entry->restore();
                        $entry->searchable();
                        $entry->videos->each(fn (Video $video) => $video->searchable());
                    });
                });
            });
        });
    }
}
