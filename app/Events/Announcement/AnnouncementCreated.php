<?php

namespace App\Events\Announcement;

use App\Contracts\Events\DiscordMessageEvent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

class AnnouncementCreated extends AnnouncementEvent implements DiscordMessageEvent
{
    use Dispatchable, SerializesModels;

    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        $announcement = $this->getAnnouncement();

        // TODO: messages shouldn't be hard-coded
        return DiscordMessage::create('Announcement Created', [
            'description' => "Announcement '{$announcement->getName()}' has been created.",
        ]);
    }

    /**
     * Get Discord channel the message will be sent to.
     *
     * @return string
     */
    public function getDiscordChannel()
    {
        return Config::get('services.discord.admin_discord_channel');
    }
}
