<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime\Theme\Entry;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiUpdatedEvent;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;

/**
 * @extends WikiUpdatedEvent<AnimeThemeEntry>
 */
class EntryUpdated extends WikiUpdatedEvent implements UpdateRelatedIndicesEvent
{
    public function __construct(AnimeThemeEntry $entry)
    {
        parent::__construct($entry);
        $this->initializeEmbedFields($entry);
    }

    /**
     * Get the model that has fired this event.
     */
    public function getModel(): AnimeThemeEntry
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Entry '**{$this->getModel()->getName()}**' has been updated.";
    }

    /**
     * Perform updates on related indices.
     */
    public function updateRelatedIndices(): void
    {
        $entry = $this->getModel();

        $entry->videos->each(fn (Video $video) => $video->searchable());
    }
}
