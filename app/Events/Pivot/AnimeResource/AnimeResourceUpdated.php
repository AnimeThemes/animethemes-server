<?php

namespace App\Events\Pivot\AnimeResource;

use App\Discord\Events\DiscordMessageEvent;
use App\Discord\Traits\HasAttributeUpdateEmbedFields;
use App\Pivots\AnimeResource;
use Illuminate\Foundation\Events\Dispatchable;
use NotificationChannels\Discord\DiscordMessage;

class AnimeResourceUpdated extends AnimeResourceEvent implements DiscordMessageEvent
{
    use Dispatchable, HasAttributeUpdateEmbedFields;

    /**
     * Create a new event instance.
     *
     * @param \App\Pivots\AnimeResource $animeResource
     * @return void
     */
    public function __construct(AnimeResource $animeResource)
    {
        parent::__construct($animeResource);
        $this->initializeEmbedFields($animeResource);
    }

    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        $anime = $this->getAnime();
        $resource = $this->getResource();

        // TODO: messages shouldn't be hard-coded
        return DiscordMessage::create('Anime Resource Updated', [
            'description' => "Resource '{$resource->link}' for Anime '{$anime->name}' has been updated.",
            'fields' => $this->getEmbedFields(),
        ]);
    }
}
