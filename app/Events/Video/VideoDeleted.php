<?php

namespace App\Events\Video;

use App\Discord\Events\DiscordMessageEvent;
use Illuminate\Foundation\Events\Dispatchable;
use NotificationChannels\Discord\DiscordMessage;

class VideoDeleted extends VideoEvent implements DiscordMessageEvent
{
    use Dispatchable;

    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        $video = $this->getVideo();

        // TODO: messages shouldn't be hard-coded
        return DiscordMessage::create('Video Deleted', [
            'description' => "Video '{$video->getName()}' has been deleted.",
        ]);
    }
}
