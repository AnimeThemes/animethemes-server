<?php

namespace App\Events\Video;

use App\Contracts\Events\DiscordMessageEvent;
use App\Concerns\Discord\HasAttributeUpdateEmbedFields;
use App\Models\Video;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Config;
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
            'description' => "Video '{$video->getName()}' has been updated.",
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
