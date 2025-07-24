<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime\Theme;

use App\Contracts\Events\CascadesRestoresEvent;
use App\Events\Base\Wiki\WikiRestoredEvent;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\SoftDeletingScope;

/**
 * Class ThemeRestored.
 *
 * @extends WikiRestoredEvent<AnimeTheme>
 */
class ThemeRestored extends WikiRestoredEvent implements CascadesRestoresEvent
{
    /**
     * The anime that the theme belongs to.
     */
    protected Anime $anime;

    public function __construct(AnimeTheme $theme)
    {
        parent::__construct($theme);
        $this->anime = $theme->anime;
    }

    /**
     * Get the model that has fired this event.
     */
    public function getModel(): AnimeTheme
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Theme '**{$this->getModel()->getName()}**' has been restored for Anime '**{$this->anime->getName()}**'.";
    }

    /**
     * Perform cascading restores.
     */
    public function cascadeRestores(): void
    {
        $theme = $this->getModel();

        $theme->animethemeentries()->withoutGlobalScope(SoftDeletingScope::class)->get()->each(function (AnimeThemeEntry $entry) {
            AnimeThemeEntry::withoutEvents(function () use ($entry) {
                $entry->restore();
                $entry->searchable();
                $entry->videos->each(fn (Video $video) => $video->searchable());
            });
        });
    }
}
