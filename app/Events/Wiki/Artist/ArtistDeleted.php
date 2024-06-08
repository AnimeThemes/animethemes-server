<?php

declare(strict_types=1);

namespace App\Events\Wiki\Artist;

use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\Artist as ArtistFilament;
use App\Models\Wiki\Artist;
use App\Nova\Resources\Wiki\Artist as ArtistResource;

/**
 * Class ArtistDeleted.
 *
 * @extends WikiDeletedEvent<Artist>
 */
class ArtistDeleted extends WikiDeletedEvent
{
    /**
     * Create a new event instance.
     *
     * @param  Artist  $artist
     */
    public function __construct(Artist $artist)
    {
        parent::__construct($artist);
    }

    /**
     * Get the model that has fired this event.
     *
     * @return Artist
     */
    public function getModel(): Artist
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
        return "Artist '**{$this->getModel()->getName()}**' has been deleted.";
    }

    /**
     * Get the message for the nova notification.
     *
     * @return string
     */
    protected function getNotificationMessage(): string
    {
        return "Artist '{$this->getModel()->getName()}' has been deleted. It will be automatically pruned in one week. Please review.";
    }

    /**
     * Get the URL for the nova notification.
     *
     * @return string
     */
    protected function getNovaNotificationUrl(): string
    {
        $uriKey = ArtistResource::uriKey();

        return "/resources/$uriKey/{$this->getModel()->getKey()}";
    }

    /**
     * Get the URL for the Filament notification.
     *
     * @return string
     */
    protected function getFilamentNotificationUrl(): string
    {
        $uriKey = ArtistFilament::getRecordSlug();

        return "/resources/$uriKey/{$this->getModel()->getKey()}";
    }
}
