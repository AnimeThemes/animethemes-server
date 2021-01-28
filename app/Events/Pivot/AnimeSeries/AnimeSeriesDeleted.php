<?php

namespace App\Events\Pivot\AnimeSeries;

use App\Contracts\Events\DiscordMessageEvent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
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
            'description' => "Anime '{$anime->getName()}' has been detached from Series '{$series->getName()}'.",
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
