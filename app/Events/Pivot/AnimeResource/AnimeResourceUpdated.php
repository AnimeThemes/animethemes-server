<?php

namespace App\Events\Pivot\AnimeResource;

use App\Concerns\Discord\HasAttributeUpdateEmbedFields;
use App\Contracts\Events\DiscordMessageEvent;
use App\Pivots\AnimeResource;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Config;
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
        return DiscordMessage::create('Resource Updated', [
            'description' => "Resource '{$resource->getName()}' for Anime '{$anime->getName()}' has been updated.",
            'fields' => $this->getEmbedFields(),
        ]);
    }

    /**
     * Get Discord channel the message will be sent to.
     *
     * @return string
     */
    public function getDiscordChannel()
    {
        return Config::get('services.discord.db_updates_discord_channel');
    }
}
