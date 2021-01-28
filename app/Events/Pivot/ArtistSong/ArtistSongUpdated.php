<?php

namespace App\Events\Pivot\ArtistSong;

use App\Contracts\Events\DiscordMessageEvent;
use App\Concerns\Discord\HasAttributeUpdateEmbedFields;
use App\Pivots\ArtistSong;
use App\Contracts\Events\UpdateRelatedIndicesEvent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

class ArtistSongUpdated extends ArtistSongEvent implements DiscordMessageEvent, UpdateRelatedIndicesEvent
{
    use Dispatchable, HasAttributeUpdateEmbedFields;

    /**
     * Create a new event instance.
     *
     * @param \App\Pivots\ArtistSong $artistSong
     * @return void
     */
    public function __construct(ArtistSong $artistSong)
    {
        parent::__construct($artistSong);
        $this->initializeEmbedFields($artistSong);
    }

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
        return DiscordMessage::create('Song Updated', [
            'description' => "Song '{$song->getName()}' for Artist '{$artist->getName()}' has been updated.",
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
