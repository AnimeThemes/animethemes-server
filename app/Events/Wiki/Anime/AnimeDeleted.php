<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime;

use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\Anime as AnimeFilament;
use App\Models\Wiki\Anime;

/**
 * @extends WikiDeletedEvent<Anime>
 */
class AnimeDeleted extends WikiDeletedEvent
{
    public function __construct(Anime $anime)
    {
        parent::__construct($anime);
    }

    /**
     * Get the model that has fired this event.
     */
    public function getModel(): Anime
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Anime '**{$this->getModel()->getName()}**' has been deleted.";
    }

    /**
     * Get the message for the filament notification.
     */
    protected function getNotificationMessage(): string
    {
        return "Anime '{$this->getModel()->getName()}' has been deleted. It will be automatically pruned in one week. Please review.";
    }

    /**
     * Get the URL for the Filament notification.
     */
    protected function getFilamentNotificationUrl(): string
    {
        return AnimeFilament::getUrl('view', ['record' => $this->getModel()]);
    }
}
