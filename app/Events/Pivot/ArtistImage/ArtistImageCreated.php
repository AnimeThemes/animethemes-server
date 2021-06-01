<?php

declare(strict_types=1);

namespace App\Events\Pivot\ArtistImage;

use App\Contracts\Events\DiscordMessageEvent;
use App\Enums\Discord\EmbedColor;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class ArtistImageCreated
 * @package App\Events\Pivot\ArtistImage
 */
class ArtistImageCreated extends ArtistImageEvent implements DiscordMessageEvent
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
        $artist = $this->getArtist();
        $image = $this->getImage();

        return DiscordMessage::create('', [
            'description' => "Image '**{$image->getName()}**' has been attached to Artist '**{$artist->getName()}**'.",
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
        return Config::get('services.discord.db_updates_discord_channel');
    }
}
