<?php

namespace App\Events\Pivot\AnimeResource;

use App\Discord\Events\DiscordMessageEvent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NotificationChannels\Discord\DiscordMessage;

class AnimeResourceDeleted extends AnimeResourceEvent implements DiscordMessageEvent
{
    use Dispatchable, SerializesModels;

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
        return DiscordMessage::create('Resource Detached', [
            'description' => "Resource '{$resource->getName()}' has been detached from Anime '{$anime->getName()}'.",
        ]);
    }
}
