<?php

declare(strict_types=1);

namespace App\Contracts\Events;

use NotificationChannels\Discord\DiscordMessage;

interface DiscordMessageEvent
{
    public function getDiscordMessage(): DiscordMessage;

    public function getDiscordChannel(): string;

    public function shouldSendDiscordMessage(): bool;
}
