<?php

declare(strict_types=1);

namespace App\Events\Announcement;

use App\Contracts\Events\DiscordMessageEvent;
use App\Enums\Services\Discord\EmbedColor;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class AnnouncementRestored.
 */
class AnnouncementRestored extends AnnouncementEvent implements DiscordMessageEvent
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
        $announcement = $this->getAnnouncement();

        return DiscordMessage::create('', [
            'description' => "Announcement '**{$announcement->getName()}**' has been restored.",
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
        return Config::get('services.discord.admin_discord_channel');
    }
}
