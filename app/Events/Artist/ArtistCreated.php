<?php

namespace App\Events\Artist;

use App\Discord\Events\DiscordMessageEvent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NotificationChannels\Discord\DiscordMessage;

class ArtistCreated extends ArtistEvent implements DiscordMessageEvent
{
    use Dispatchable, SerializesModels;

    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        $artist = $this->getArtist();

        // TODO: messages shouldn't be hard-coded
        return DiscordMessage::create('Artist Created', [
            'description' => "Artist '{$artist->getName()}' has been created.",
        ]);
    }
}
