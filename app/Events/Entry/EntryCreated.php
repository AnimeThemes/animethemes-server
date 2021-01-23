<?php

namespace App\Events\Entry;

use App\Discord\Events\DiscordMessageEvent;
use App\Scout\Events\UpdateRelatedIndicesEvent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NotificationChannels\Discord\DiscordMessage;

class EntryCreated extends EntryEvent implements DiscordMessageEvent, UpdateRelatedIndicesEvent
{
    use Dispatchable, SerializesModels;

    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        // TODO: messages shouldn't be hard-coded
        return DiscordMessage::create('Entry Created', [
            'description' => "Entry '{$this->getName()}' has been created.",
        ]);
    }

    /**
     * Perform updates on related indices.
     *
     * @return void
     */
    public function updateRelatedIndices()
    {
        $entry = $this->getEntry();

        $entry->videos->searchable();
    }
}
