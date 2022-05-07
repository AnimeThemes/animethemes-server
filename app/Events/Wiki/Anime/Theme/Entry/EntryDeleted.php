<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime\Theme\Entry;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Nova\Resources\Wiki\Anime\Theme\Entry as EntryResource;

/**
 * Class EntryDeleted.
 *
 * @extends WikiDeletedEvent<AnimeThemeEntry>
 */
class EntryDeleted extends WikiDeletedEvent implements UpdateRelatedIndicesEvent
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
        return "Entry '**{$this->getModel()->getName()}**' has been deleted.";
    }

    /**
     * Get the message for the nova notification.
     *
     * @return string
     */
    protected function getNotificationMessage(): string
    {
        return "Entry '{$this->getModel()->getName()}' has been deleted. It will be automatically pruned in one week. Please review.";
    }

    /**
     * Get the URL for the nova notification.
     *
     * @return string
     */
    protected function getNotificationUrl(): string
    {
        $uriKey = EntryResource::uriKey();

        return "/resources/$uriKey/{$this->getModel()->getKey()}";
    }

    /**
     * Perform updates on related indices.
     *
     * @return void
     */
    public function updateRelatedIndices(): void
    {
        $entry = $this->getModel();

        $videos = $entry->videos;
        $videos->each(fn (Video $video) => $video->searchable());
    }
}
