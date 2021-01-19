<?php

namespace App\Events\Anime;

use NotificationChannels\Discord\DiscordMessage;

// TODO: Events aren't firing because serialized model is ded when we handle the notification.
class AnimeDeleted extends AnimeEvent
{
    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        $anime = $this->getAnime();

        // TODO: messages shouldn't be hard-coded
        return DiscordMessage::create('Anime Deleted', [
            'description' => "Anime '{$anime->name}' has been deleted.",
        ]);
    }
}
