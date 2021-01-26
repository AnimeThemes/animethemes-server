<?php

namespace App\Events\Pivot\VideoEntry;

use App\Discord\Events\DiscordMessageEvent;
use App\Scout\Events\UpdateRelatedIndicesEvent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NotificationChannels\Discord\DiscordMessage;

class VideoEntryDeleted extends VideoEntryEvent implements DiscordMessageEvent, UpdateRelatedIndicesEvent
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
        return DiscordMessage::create('Video Detached', [
            'description' => "Video '{$video->filename}' has been detached from Entry '{$entry->getName()}'.",
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
