<?php

declare(strict_types=1);

namespace App\Events\Billing\Balance;

use App\Concerns\Services\Discord\HasAttributeUpdateEmbedFields;
use App\Contracts\Events\DiscordMessageEvent;
use App\Enums\Services\Discord\EmbedColor;
use App\Models\Billing\Balance;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class BalanceUpdated.
 */
class BalanceUpdated extends BalanceEvent implements DiscordMessageEvent
{
    use Dispatchable;
    use HasAttributeUpdateEmbedFields;

    /**
     * Create a new event instance.
     *
     * @param Balance $balance
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
     * @return DiscordMessage
     */
    public function getDiscordMessage(): DiscordMessage
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
    public function getDiscordChannel(): string
    {
        return Config::get('services.discord.db_updates_discord_channel');
    }
}
