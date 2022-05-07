<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime\Theme\Entry;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\BaseEvent;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;

/**
 * Class EntryDeleting.
 *
 * @extends BaseEvent<AnimeThemeEntry>
 */
class EntryDeleting extends BaseEvent implements UpdateRelatedIndicesEvent
{
    /**
     * Create a new event instance.
     *
     * @param  AnimeThemeEntry  $entry
     */
    public function __construct(AnimeThemeEntry $entry)
    {
        parent::__construct($entry);
    }

    /**
     * Get the model that has fired this event.
     *
     * @return AnimeThemeEntry
     */
    public function getModel(): AnimeThemeEntry
    {
        return $this->model;
    }

    /**
     * Perform updates on related indices.
     *
     * @return void
     */
    public function updateRelatedIndices(): void
    {
        $entry = $this->getModel();

        if ($entry->isForceDeleting()) {
            // refresh video documents by detaching entry
            $videos = $entry->videos;
            $entry->videos()->detach();
            $videos->each(fn (Video $video) => $video->searchable());
        }
    }
}
