<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime\Theme;

use App\Contracts\Events\CascadesRestoresEvent;
use App\Events\Base\Wiki\WikiRestoredEvent;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\SoftDeletingScope;

/**
 * @extends WikiRestoredEvent<AnimeTheme>
 */
class ThemeRestored extends WikiRestoredEvent implements CascadesRestoresEvent
{
    protected function getDiscordMessageDescription(): string
    {
        return "Theme '**{$this->getModel()->getName()}**' has been restored for Anime '**{$this->getModel()->anime->getName()}**'.";
    }

    public function cascadeRestores(): void
    {
        $theme = $this->getModel();

        $theme->animethemeentries()->withoutGlobalScope(SoftDeletingScope::class)->get()->each(function (AnimeThemeEntry $entry): void {
            AnimeThemeEntry::withoutEvents(function () use ($entry): void {
                $entry->restore();
                $entry->searchable();
                $entry->videos->each(fn (Video $video) => $video->searchable());
            });
        });
    }
}
