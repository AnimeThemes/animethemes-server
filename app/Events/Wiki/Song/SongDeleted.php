<?php

declare(strict_types=1);

namespace App\Events\Wiki\Song;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Models\Wiki\Video;
use App\Nova\Resources\Wiki\Song as SongResource;

/**
 * Class SongDeleted.
 *
 * @extends WikiDeletedEvent<Song>
 */
class SongDeleted extends WikiDeletedEvent implements UpdateRelatedIndicesEvent
{
    /**
     * Create a new event instance.
     *
     * @param  Song  $song
     */
    public function __construct(Song $song)
    {
        parent::__construct($song);
    }

    /**
     * Get the model that has fired this event.
     *
     * @return Song
     */
    public function getModel(): Song
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
        return "Song '**{$this->getModel()->getName()}**' has been deleted.";
    }

    /**
     * Get the message for the nova notification.
     *
     * @return string
     */
    protected function getNotificationMessage(): string
    {
        return "Song '{$this->getModel()->getName()}' has been deleted. It will be automatically pruned in one week. Please review.";
    }

    /**
     * Get the URL for the nova notification.
     *
     * @return string
     */
    protected function getNovaNotificationUrl(): string
    {
        $uriKey = SongResource::uriKey();

        return "/resources/$uriKey/{$this->getModel()->getKey()}";
    }

    /**
     * Perform updates on related indices.
     *
     * @return void
     */
    public function updateRelatedIndices(): void
    {
        $song = $this->getModel()->load([Song::RELATION_ARTISTS, Song::RELATION_VIDEOS]);

        $artists = $song->artists;
        $artists->each(fn (Artist $artist) => $artist->searchable());

        $song->animethemes->each(function (AnimeTheme $theme) {
            $theme->searchable();
            $theme->animethemeentries->each(function (AnimeThemeEntry $entry) {
                $entry->searchable();
                $entry->videos->each(fn (Video $video) => $video->searchable());
            });
        });
    }
}
