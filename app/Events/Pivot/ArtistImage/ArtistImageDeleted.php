<?php

namespace App\Events\Pivot\ArtistImage;

use App\Discord\Events\DiscordMessageEvent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NotificationChannels\Discord\DiscordMessage;

class ArtistImageDeleted extends ArtistImageEvent implements DiscordMessageEvent
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
        $image = $this->getImage();

        // TODO: messages shouldn't be hard-coded
        return DiscordMessage::create('Image Detached', [
            'description' => "Image '{$image->getName()}' has been detached from Artist '{$artist->getName()}'.",
        ]);
    }
}
