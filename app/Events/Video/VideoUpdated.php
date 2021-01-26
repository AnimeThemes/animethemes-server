<?php

namespace App\Events\Video;

use App\Discord\Events\DiscordMessageEvent;
use App\Discord\Traits\HasAttributeUpdateEmbedFields;
use App\Models\Video;
use Illuminate\Foundation\Events\Dispatchable;
use NotificationChannels\Discord\DiscordMessage;

class VideoUpdated extends VideoEvent implements DiscordMessageEvent
{
    use Dispatchable, HasAttributeUpdateEmbedFields;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Video $video
     * @return void
     */
    public function __construct(Video $video)
    {
        parent::__construct($video);
        $this->initializeEmbedFields($video);
    }

    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        $video = $this->getVideo();

        // TODO: messages shouldn't be hard-coded
        return DiscordMessage::create('Video Updated', [
            'description' => "Video '{$video->filename}' has been updated.",
            'fields' => $this->getEmbedFields(),
        ]);
    }
}
