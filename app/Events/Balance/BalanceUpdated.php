<?php

namespace App\Events\Balance;

use App\Concerns\Discord\HasAttributeUpdateEmbedFields;
use App\Contracts\Events\DiscordMessageEvent;
use App\Enums\Discord\EmbedColor;
use App\Models\Balance;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

class BalanceUpdated extends BalanceEvent implements DiscordMessageEvent
{
    use Dispatchable, HasAttributeUpdateEmbedFields;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Balance $balance
     * @return void
     */
    public function __construct(Balance $balance)
    {
        parent::__construct($balance);
        $this->initializeEmbedFields($balance);
    }

    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        $balance = $this->getBalance();

        return DiscordMessage::create('', [
            'description' => "Balance '**{$balance->getName()}**' has been updated.",
            'fields' => $this->getEmbedFields(),
            'color' => EmbedColor::YELLOW,
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
