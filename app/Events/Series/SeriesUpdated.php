<?php

namespace App\Events\Series;

use App\Concerns\Discord\HasAttributeUpdateEmbedFields;
use App\Contracts\Events\DiscordMessageEvent;
use App\Models\Series;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

class SeriesUpdated extends SeriesEvent implements DiscordMessageEvent
{
    use Dispatchable, HasAttributeUpdateEmbedFields;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Series $series
     * @return void
     */
    public function __construct(Series $series)
    {
        parent::__construct($series);
        $this->initializeEmbedFields($series);
    }

    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        $series = $this->getSeries();

        // TODO: messages shouldn't be hard-coded
        return DiscordMessage::create('Series Updated', [
            'description' => "Series '{$series->getName()}' has been updated.",
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
}
