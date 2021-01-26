<?php

namespace App\Events\Entry;

use App\Discord\Events\DiscordMessageEvent;
use App\Discord\Traits\HasAttributeUpdateEmbedFields;
use App\Models\Entry;
use App\Scout\Events\UpdateRelatedIndicesEvent;
use Illuminate\Foundation\Events\Dispatchable;
use NotificationChannels\Discord\DiscordMessage;

class EntryUpdated extends EntryEvent implements DiscordMessageEvent, UpdateRelatedIndicesEvent
{
    use Dispatchable, HasAttributeUpdateEmbedFields;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Entry $entry
     * @return void
     */
    public function __construct(Entry $entry)
    {
        parent::__construct($entry);
        $this->initializeEmbedFields($entry);
    }

    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        $entry = $this->getEntry();

        // TODO: messages shouldn't be hard-coded
        return DiscordMessage::create('Entry Updated', [
            'description' => "Entry '{$entry->getName()}' has been updated.",
            'fields' => $this->getEmbedFields(),
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
