<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime\Theme\Entry;

use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;

/**
 * Class EntryEvent.
 */
abstract class EntryEvent
{
    /**
     * Create a new event instance.
     *
     * @param  AnimeThemeEntry  $entry
     * @return void
     */
    public function __construct(protected AnimeThemeEntry $entry)
    {
    }

    /**
     * Get the entry that has fired this event.
     *
     * @return AnimeThemeEntry
     */
    public function getEntry(): AnimeThemeEntry
    {
        return $this->entry;
    }
}
