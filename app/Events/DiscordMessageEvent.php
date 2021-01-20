<?php

namespace App\Events;

interface DiscordMessageEvent
{
    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage();
}
