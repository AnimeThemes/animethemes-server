<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime\Theme\Entry;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiCreatedEvent;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;

/**
 * Class EntryCreated.
 *
 * @extends WikiCreatedEvent<AnimeThemeEntry>
 */
class EntryCreated extends WikiCreatedEvent implements UpdateRelatedIndicesEvent
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
     * Get the description for the Discord message payload.
     *
     * @return string
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Entry '**{$this->getModel()->getName()}**' has been created.";
    }

    /**
     * Perform updates on related indices.
     *
     * @return void
     */
    public function updateRelatedIndices(): void
    {
        $entry = $this->getModel();

        $entry->videos->each(fn (Video $video) => $video->searchable());
    }
}
