<?php

namespace App\Events\Image;

use App\Discord\Events\DiscordMessageEvent;
use Illuminate\Foundation\Events\Dispatchable;
use NotificationChannels\Discord\DiscordMessage;

class ImageDeleted extends ImageEvent implements DiscordMessageEvent
{
    use Dispatchable;

    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        $image = $this->getImage();

        // TODO: messages shouldn't be hard-coded
        return DiscordMessage::create('Image Deleted', [
            'description' => "Image '{$image->path}' has been deleted.",
        ]);
    }
}
