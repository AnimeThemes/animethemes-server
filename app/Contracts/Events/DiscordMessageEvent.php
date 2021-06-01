<?php

declare(strict_types=1);

namespace App\Contracts\Events;

use NotificationChannels\Discord\DiscordMessage;

/**
 * Interface DiscordMessageEvent
 * @package App\Contracts\Events
 */
interface DiscordMessageEvent
{
    /**
     * Get Discord message payload.
     *
     * @return DiscordMessage
     */
    public function getDiscordMessage(): DiscordMessage;

    /**
     * Get Discord channel the message will be sent to.
     *
     * @return string
     */
    public function getDiscordChannel(): string;
}
