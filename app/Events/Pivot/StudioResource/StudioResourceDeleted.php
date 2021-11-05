<?php
declare(strict_types=1);

namespace App\Events\Pivot\StudioResource;

use App\Contracts\Events\DiscordMessageEvent;
use App\Enums\Services\Discord\EmbedColor;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class StudioResourceDeleted.
 */
class StudioResourceDeleted extends StudioResourceEvent implements DiscordMessageEvent
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
        $studio = $this->getStudio();
        $resource = $this->getResource();

        return DiscordMessage::create('', [
            'description' => "Resource '**{$resource->getName()}**' has been detached from Studio '**{$studio->getName()}**'.",
            'color' => EmbedColor::RED,
        ]);
    }

    /**
     * Get Discord channel the mesasge will be sent to.
     * 
     * @return string
     */
    public function getDiscordChannel():string
    {
        return Config::get('services.discord.db_updates_discord_channel');
    }
}

?>