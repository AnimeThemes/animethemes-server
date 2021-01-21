<?php

namespace App\Events\Anime;

use App\Discord\Events\DiscordMessageEvent;
use Illuminate\Foundation\Events\Dispatchable;
use NotificationChannels\Discord\DiscordMessage;

class AnimeDeleted extends AnimeEvent implements DiscordMessageEvent
{
    use Dispatchable;

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
