<?php

declare(strict_types=1);

namespace App\Events\Wiki\Artist;

use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\Artist as ArtistFilament;
use App\Models\Wiki\Artist;

/**
 * Class ArtistDeleted.
 *
 * @extends WikiDeletedEvent<Artist>
 */
class ArtistDeleted extends WikiDeletedEvent
{
    public function __construct(Artist $artist)
    {
        parent::__construct($artist);
    }

    /**
     * Get the model that has fired this event.
     */
    public function getModel(): Artist
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Artist '**{$this->getModel()->getName()}**' has been deleted.";
    }

    /**
     * Get the message for the filament notification.
     */
    protected function getNotificationMessage(): string
    {
        return "Artist '{$this->getModel()->getName()}' has been deleted. It will be automatically pruned in one week. Please review.";
    }

    /**
     * Get the URL for the Filament notification.
     */
    protected function getFilamentNotificationUrl(): string
    {
        return ArtistFilament::getUrl('view', ['record' => $this->getModel()]);
    }
}
