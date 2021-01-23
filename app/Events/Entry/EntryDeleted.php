<?php

namespace App\Events\Entry;

use App\Discord\Events\DiscordMessageEvent;
use Illuminate\Foundation\Events\Dispatchable;
use NotificationChannels\Discord\DiscordMessage;

class EntryDeleted extends EntryEvent implements DiscordMessageEvent
{
    use Dispatchable;

    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        // TODO: messages shouldn't be hard-coded
        return DiscordMessage::create('Entry Deleted', [
            'description' => "Entry '{$this->getName()}' has been deleted.",
        ]);
    }
}
