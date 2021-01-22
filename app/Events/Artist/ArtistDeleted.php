<?php

namespace App\Events\Artist;

use App\Discord\Events\DiscordMessageEvent;
use Illuminate\Foundation\Events\Dispatchable;
use NotificationChannels\Discord\DiscordMessage;

class ArtistDeleted extends ArtistEvent implements DiscordMessageEvent
{
    use Dispatchable;

    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        $artist = $this->getArtist();

        // TODO: messages shouldn't be hard-coded
        return DiscordMessage::create('Artist Deleted', [
            'description' => "Artist '{$artist->name}' has been deleted.",
        ]);
    }
}
