<?php declare(strict_types=1);

namespace App\Events\Announcement;

use App\Contracts\Events\DiscordMessageEvent;
use App\Enums\Discord\EmbedColor;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class AnnouncementDeleted
 * @package App\Events\Announcement
 */
class AnnouncementDeleted extends AnnouncementEvent implements DiscordMessageEvent
{
    use Dispatchable;

    /**
     * Get Discord message payload.
     *
     * @return DiscordMessage
     */
    public function getDiscordMessage(): DiscordMessage
    {
        $announcement = $this->getAnnouncement();

        return DiscordMessage::create('', [
            'description' => "Announcement '**{$announcement->getName()}**' has been deleted.",
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
        return Config::get('services.discord.admin_discord_channel');
    }
}
