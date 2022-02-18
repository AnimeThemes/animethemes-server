<?php

declare(strict_types=1);

namespace App\Events\Document\Page;

use App\Contracts\Events\DiscordMessageEvent;
use App\Enums\Services\Discord\EmbedColor;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class PageDeleted.
 */
class PageDeleted extends PageEvent implements DiscordMessageEvent
{
    use Dispatchable;

    /**
     * Get Discord message payload.
     *
     * @return DiscordMessage
     */
    public function getDiscordMessage(): DiscordMessage
    {
        $page = $this->getPage();

        return DiscordMessage::create('', [
            'description' => "Page '**{$page->getName()}**' has been deleted.",
            'color' => EmbedColor::RED,
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
