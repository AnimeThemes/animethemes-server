<?php

namespace App\Events\Entry;

use App\Concerns\Discord\HasAttributeUpdateEmbedFields;
use App\Contracts\Events\DiscordMessageEvent;
use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Models\Entry;
use App\Models\Video;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Config;
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

        return DiscordMessage::create('Entry Updated', [
            'description' => "Entry '{$entry->getName()}' has been updated.",
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

    /**
     * Perform updates on related indices.
     *
     * @return void
     */
    public function updateRelatedIndices()
    {
        $entry = $this->getEntry();

        $entry->videos->each(function (Video $video) {
            $video->searchable();
        });
    }
}
