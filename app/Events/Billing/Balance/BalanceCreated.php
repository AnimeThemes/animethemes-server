<?php

declare(strict_types=1);

namespace App\Events\Billing\Balance;

use App\Contracts\Events\DiscordMessageEvent;
use App\Enums\Services\Discord\EmbedColor;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class BalanceCreated.
 */
class BalanceCreated extends BalanceEvent implements DiscordMessageEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Get Discord message payload.
     *
     * @return DiscordMessage
     */
    public function getDiscordMessage(): DiscordMessage
    {
        $balance = $this->getBalance();

        return DiscordMessage::create('', [
            'description' => "Balance '**{$balance->getName()}**' has been created.",
            'color' => EmbedColor::GREEN,
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
