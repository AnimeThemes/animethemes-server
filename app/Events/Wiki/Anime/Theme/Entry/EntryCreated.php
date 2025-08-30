<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime\Theme\Entry;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiCreatedEvent;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;

/**
 * @extends WikiCreatedEvent<AnimeThemeEntry>
 */
class EntryCreated extends WikiCreatedEvent implements UpdateRelatedIndicesEvent
{
    public function __construct(AnimeThemeEntry $entry)
    {
        parent::__construct($entry);
    }

    public function getModel(): AnimeThemeEntry
    {
        return $this->model;
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Entry '**{$this->getModel()->getName()}**' has been created.";
    }

    public function updateRelatedIndices(): void
    {
        $entry = $this->getModel();

        $entry->videos->each(fn (Video $video) => $video->searchable());
    }
}
