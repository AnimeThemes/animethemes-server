<?php

namespace App\Events\Anime;

use NotificationChannels\Discord\DiscordMessage;

class AnimeCreated extends AnimeEvent
{
    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        $anime = $this->getAnime();

        // localize messages
        return DiscordMessage::create('Anime Created', [
            'description' => "Anime '{$anime->name}' has been created.",
        ]);
    }
}
