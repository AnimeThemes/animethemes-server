<?php

namespace App\Events\Image;

use App\Discord\Events\DiscordMessageEvent;
use App\Discord\Traits\HasAttributeUpdateEmbedFields;
use App\Models\Image;
use Illuminate\Foundation\Events\Dispatchable;
use NotificationChannels\Discord\DiscordMessage;

class ImageUpdated extends ImageEvent implements DiscordMessageEvent
{
    use Dispatchable, HasAttributeUpdateEmbedFields;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Image $image
     * @return void
     */
    public function __construct(Image $image)
    {
        parent::__construct($image);
        $this->initializeEmbedFields($image);
    }

    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        $image = $this->getImage();

        // TODO: messages shouldn't be hard-coded
        return DiscordMessage::create('Image Updated', [
            'description' => "Image '{$image->path}' has been updated.",
            'fields' => $this->getEmbedFields(),
        ]);
    }
}
