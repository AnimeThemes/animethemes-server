<?php

namespace App\Events\Pivot\ArtistResource;

use App\Discord\Events\DiscordMessageEvent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NotificationChannels\Discord\DiscordMessage;

class ArtistResourceCreated extends ArtistResourceEvent implements DiscordMessageEvent
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
        $resource = $this->getResource();

        // TODO: messages shouldn't be hard-coded
        return DiscordMessage::create('Resource Attached', [
            'description' => "Resource '{$resource->getName()}' has been attached to Artist '{$artist->getName()}'.",
        ]);
    }
}
