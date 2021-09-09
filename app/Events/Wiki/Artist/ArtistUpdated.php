<?php

declare(strict_types=1);

namespace App\Events\Wiki\Artist;

use App\Concerns\Services\Discord\HasAttributeUpdateEmbedFields;
use App\Contracts\Events\DiscordMessageEvent;
use App\Enums\Services\Discord\EmbedColor;
use App\Models\Wiki\Artist;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class ArtistUpdated.
 */
class ArtistUpdated extends ArtistEvent implements DiscordMessageEvent
{
    use Dispatchable;
    use HasAttributeUpdateEmbedFields;

    /**
     * Create a new event instance.
     *
     * @param  Artist  $artist
     * @return void
     */
    public function __construct(Artist $artist)
    {
        parent::__construct($artist);
        $this->initializeEmbedFields($artist);
    }

    /**
     * Get Discord message payload.
     *
     * @return DiscordMessage
     */
    public function getDiscordMessage(): DiscordMessage
    {
        $artist = $this->getArtist();

        return DiscordMessage::create('', [
            'description' => "Artist '**{$artist->getName()}**' has been updated.",
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
        return Config::get('services.discord.db_updates_discord_channel');
    }
}
