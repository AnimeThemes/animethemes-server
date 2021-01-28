<?php

namespace App\Events\Artist;

use App\Discord\Events\DiscordMessageEvent;
use App\Discord\Traits\HasAttributeUpdateEmbedFields;
use App\Models\Artist;
use Illuminate\Foundation\Events\Dispatchable;
use NotificationChannels\Discord\DiscordMessage;

class ArtistUpdated extends ArtistEvent implements DiscordMessageEvent
{
    use Dispatchable, HasAttributeUpdateEmbedFields;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Artist $artist
     * @return void
     */
    public function __construct(Artist $artist)
    {
        parent::__construct($artist);
        $this->initializeEmbedFields($artist);
    }

    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        $artist = $this->getArtist();

        // TODO: messages shouldn't be hard-coded
        return DiscordMessage::create('Artist Updated', [
            'description' => "Artist '{$artist->getName()}' has been updated.",
            'fields' => $this->getEmbedFields(),
        ]);
    }
}
