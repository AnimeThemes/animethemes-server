<?php

namespace App\Events\Series;

use App\Contracts\Events\DiscordMessageEvent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

class SeriesRestored extends SeriesEvent implements DiscordMessageEvent
{
    use Dispatchable, SerializesModels;

    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        $series = $this->getSeries();

        return DiscordMessage::create('Series Restored', [
            'description' => "Series '{$series->getName()}' has been restored.",
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