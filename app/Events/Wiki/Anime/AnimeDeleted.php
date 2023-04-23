<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime;

use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Models\Wiki\Anime;
use App\Nova\Resources\Wiki\Anime as AnimeResource;

/**
 * Class AnimeDeleted.
 *
 * @extends WikiDeletedEvent<Anime>
 */
class AnimeDeleted extends WikiDeletedEvent
{
    /**
     * Create a new event instance.
     *
     * @param  Anime  $anime
     */
    public function __construct(Anime $anime)
    {
        parent::__construct($anime);
    }

    /**
     * Get the model that has fired this event.
     *
     * @return Anime
     */
    public function getModel(): Anime
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
        return "Anime '**{$this->getModel()->getName()}**' has been deleted.";
    }

    /**
     * Get the message for the nova notification.
     *
     * @return string
     */
    protected function getNotificationMessage(): string
    {
        return "Anime '{$this->getModel()->getName()}' has been deleted. It will be automatically pruned in one week. Please review.";
    }

    /**
     * Get the URL for the nova notification.
     *
     * @return string
     */
    protected function getNovaNotificationUrl(): string
    {
        $uriKey = AnimeResource::uriKey();

        return "/resources/$uriKey/{$this->getModel()->getKey()}";
    }
}
