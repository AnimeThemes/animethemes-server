<?php

namespace App\Events\Series;

use App\Discord\Events\DiscordMessageEvent;
use Illuminate\Foundation\Events\Dispatchable;
use NotificationChannels\Discord\DiscordMessage;

class SeriesDeleted extends SeriesEvent implements DiscordMessageEvent
{
    use Dispatchable;

    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        $series = $this->getSeries();

        // TODO: messages shouldn't be hard-coded
        return DiscordMessage::create('Series Deleted', [
            'description' => "Series '{$series->getName()}' has been deleted.",
        ]);
    }
}
