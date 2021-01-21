<?php

namespace App\Discord\Events;

interface DiscordMessageEvent
{
    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage();
}
