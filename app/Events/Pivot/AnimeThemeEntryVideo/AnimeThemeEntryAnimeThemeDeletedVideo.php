<?php

declare(strict_types=1);

namespace App\Events\Pivot\AnimeThemeEntryVideo;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Pivot\PivotDeletedEvent;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Pivots\AnimeThemeEntryVideo;

/**
 * Class AnimeThemeEntryAnimeThemeDeletedVideo.
 *
 * @extends PivotDeletedEvent<AnimeThemeEntry, Video>
 */
class AnimeThemeEntryAnimeThemeDeletedVideo extends PivotDeletedEvent implements UpdateRelatedIndicesEvent
{
    /**
     * Create a new event instance.
     *
     * @param  AnimeThemeEntryVideo  $entryVideo
     */
    public function __construct(AnimeThemeEntryVideo $entryVideo)
    {
        parent::__construct($entryVideo->animethemeentry, $entryVideo->video);
    }

    /**
     * Get the description for the Discord message payload.
     *
     * @return string
     */
    protected function getDiscordMessageDescription(): string
    {
        $foreign = $this->getForeign();
        $related = $this->getRelated();

        return "Video '**{$foreign->getName()}**' has been detached from Entry '**{$related->getName()}**'.";
    }

    /**
     * Perform updates on related indices.
     *
     * @return void
     */
    public function updateRelatedIndices(): void
    {
        // refresh video document
        $video = $this->getForeign();
        $video->searchable();
    }
}
