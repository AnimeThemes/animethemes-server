<?php

declare(strict_types=1);

namespace App\Events\Wiki\Song;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\Song as SongFilament;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Song;
use App\Models\Wiki\Video;

/**
 * @extends WikiDeletedEvent<Song>
 */
class SongDeleted extends WikiDeletedEvent implements UpdateRelatedIndicesEvent
{
    public function __construct(Song $song)
    {
        parent::__construct($song);
    }

    public function getModel(): Song
    {
        return $this->model;
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Song '**{$this->getModel()->getName()}**' has been deleted.";
    }

    protected function getNotificationMessage(): string
    {
        return "Song '{$this->getModel()->getName()}' has been deleted. It will be automatically pruned in one week. Please review.";
    }

    protected function getFilamentNotificationUrl(): string
    {
        return SongFilament::getUrl('view', ['record' => $this->getModel()]);
    }

    public function updateRelatedIndices(): void
    {
        $song = $this->getModel()->load([Song::RELATION_VIDEOS]);

        $song->animethemes->each(function (AnimeTheme $theme): void {
            $theme->searchable();
            $theme->animethemeentries->each(function (AnimeThemeEntry $entry): void {
                $entry->searchable();
                $entry->videos->each(fn (Video $video) => $video->searchable());
            });
        });
    }
}
