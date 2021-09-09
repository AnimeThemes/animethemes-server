<?php

declare(strict_types=1);

namespace App\Events\Admin\Announcement;

use App\Concerns\Services\Discord\HasAttributeUpdateEmbedFields;
use App\Contracts\Events\DiscordMessageEvent;
use App\Enums\Services\Discord\EmbedColor;
use App\Models\Admin\Announcement;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class AnnouncementUpdated.
 */
class AnnouncementUpdated extends AnnouncementEvent implements DiscordMessageEvent
{
    use Dispatchable;
    use HasAttributeUpdateEmbedFields;

    /**
     * Create a new event instance.
     *
     * @param  Announcement  $announcement
     * @return void
     */
    public function __construct(Announcement $announcement)
    {
        parent::__construct($announcement);
        $this->initializeEmbedFields($announcement);
    }

    /**
     * Get Discord message payload.
     *
     * @return DiscordMessage
     */
    public function getDiscordMessage(): DiscordMessage
    {
        $announcement = $this->getAnnouncement();

        return DiscordMessage::create('', [
            'description' => "Announcement '**{$announcement->getName()}**' has been updated.",
            'fields' => $this->getEmbedFields(),
            'color' => EmbedColor::YELLOW,
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
