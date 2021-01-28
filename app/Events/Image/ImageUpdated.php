<?php

namespace App\Events\Image;

use App\Contracts\Events\DiscordMessageEvent;
use App\Concerns\Discord\HasAttributeUpdateEmbedFields;
use App\Models\Image;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Config;
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
            'description' => "Image '{$image->getName()}' has been updated.",
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
}
