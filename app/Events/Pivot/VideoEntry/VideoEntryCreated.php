<?php

namespace App\Events\Pivot\VideoEntry;

use App\Discord\Events\DiscordMessageEvent;
use App\Scout\Events\UpdateRelatedIndicesEvent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NotificationChannels\Discord\DiscordMessage;

class VideoEntryCreated extends VideoEntryEvent implements DiscordMessageEvent, UpdateRelatedIndicesEvent
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
        $entry = $this->getEntry();

        // TODO: messages shouldn't be hard-coded
        return DiscordMessage::create('Video Attached', [
            'description' => "Video '{$video->filename}' has been attached to Entry '{$entry->getName()}'.",
        ]);
    }

    /**
     * Perform updates on related indices.
     *
     * @return void
     */
    public function updateRelatedIndices()
    {
        // refresh video document
        $video = $this->getVideo();
        $video->searchable();
    }
}
