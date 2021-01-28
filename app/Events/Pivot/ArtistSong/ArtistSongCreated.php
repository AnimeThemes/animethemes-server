<?php

namespace App\Events\Pivot\ArtistSong;

use App\Discord\Events\DiscordMessageEvent;
use App\Scout\Events\UpdateRelatedIndicesEvent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NotificationChannels\Discord\DiscordMessage;

class ArtistSongCreated extends ArtistSongEvent implements DiscordMessageEvent, UpdateRelatedIndicesEvent
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
        $song = $this->getSong();

        // TODO: messages shouldn't be hard-coded
        return DiscordMessage::create('Song Attached', [
            'description' => "Song '{$song->getName()}' has been attached to Artist '{$artist->getName()}'.",
        ]);
    }

    /**
     * Perform updates on related indices.
     *
     * @return void
     */
    public function updateRelatedIndices()
    {
        // refresh artist document
        $artist = $this->getArtist();
        $artist->searchable();
    }
}
