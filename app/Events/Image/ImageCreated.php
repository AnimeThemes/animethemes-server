<?php

namespace App\Events\Image;

use App\Discord\Events\DiscordMessageEvent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NotificationChannels\Discord\DiscordMessage;

class ImageCreated extends ImageEvent implements DiscordMessageEvent
{
    use Dispatchable, SerializesModels;

    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        $image = $this->getImage();

        // TODO: messages shouldn't be hard-coded
        return DiscordMessage::create('Image Created', [
            'description' => "Image '{$image->getName()}' has been created.",
        ]);
    }
}
