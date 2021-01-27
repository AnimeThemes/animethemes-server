<?php

namespace App\Events\Pivot\AnimeImage;

use App\Discord\Events\DiscordMessageEvent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NotificationChannels\Discord\DiscordMessage;

class AnimeImageDeleted extends AnimeImageEvent implements DiscordMessageEvent
{
    use Dispatchable, SerializesModels;

    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        $anime = $this->getAnime();
        $image = $this->getImage();

        // TODO: messages shouldn't be hard-coded
        return DiscordMessage::create('Image Detached', [
            'description' => "Image '{$image->path}' has been detached from Anime '{$anime->name}'.",
        ]);
    }
}
