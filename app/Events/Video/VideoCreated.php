<?php

namespace App\Events\Video;

use App\Discord\Events\DiscordMessageEvent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NotificationChannels\Discord\DiscordMessage;

class VideoCreated extends VideoEvent implements DiscordMessageEvent
{
    use Dispatchable, SerializesModels;

    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        $video = $this->getVideo();

        // TODO: messages shouldn't be hard-coded
        return DiscordMessage::create('Video Created', [
            'description' => "Video '{$video->filename}' has been created.",
        ]);
    }
}
