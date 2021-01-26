<?php

namespace App\Events\Pivot\AnimeSeries;

use App\Discord\Events\DiscordMessageEvent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NotificationChannels\Discord\DiscordMessage;

class AnimeSeriesDeleted extends AnimeSeriesEvent implements DiscordMessageEvent
{
    use Dispatchable, SerializesModels;

    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        $anime = $this->getAnime();
        $series = $this->getSeries();

        // TODO: messages shouldn't be hard-coded
        return DiscordMessage::create('Anime Detached', [
            'description' => "Anime '{$anime->name}' has been detached from Series '{$series->name}'.",
        ]);
    }
}
