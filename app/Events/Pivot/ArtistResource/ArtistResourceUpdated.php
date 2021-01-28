<?php

namespace App\Events\Pivot\ArtistResource;

use App\Discord\Events\DiscordMessageEvent;
use App\Discord\Traits\HasAttributeUpdateEmbedFields;
use App\Pivots\ArtistResource;
use Illuminate\Foundation\Events\Dispatchable;
use NotificationChannels\Discord\DiscordMessage;

class ArtistResourceUpdated extends ArtistResourceEvent implements DiscordMessageEvent
{
    use Dispatchable, HasAttributeUpdateEmbedFields;

    /**
     * Create a new event instance.
     *
     * @param \App\Pivots\ArtistResource $artistResource
     * @return void
     */
    public function __construct(ArtistResource $artistResource)
    {
        parent::__construct($artistResource);
        $this->initializeEmbedFields($artistResource);
    }

    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        $artist = $this->getArtist();
        $resource = $this->getResource();

        // TODO: messages shouldn't be hard-coded
        return DiscordMessage::create('Resource Updated', [
            'description' => "Resource '{$resource->getName()}' for Artist '{$artist->getName()}' has been updated.",
            'fields' => $this->getEmbedFields(),
        ]);
    }
}
