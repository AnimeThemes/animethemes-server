<?php

declare(strict_types=1);

namespace App\Contracts\Events;

use NotificationChannels\Discord\DiscordMessage;

interface DiscordMessageEvent
{
    /**
     * Get Discord message payload.
     */
    public function getDiscordMessage(): DiscordMessage;

    /**
     * Get Discord channel the message will be sent to.
     */
    public function getDiscordChannel(): string;

    /**
     * Determine if the message should be sent.
     */
    public function shouldSendDiscordMessage(): bool;
}
