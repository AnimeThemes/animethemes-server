<?php

declare(strict_types=1);

namespace App\Events\Pivot\ArtistSong;

use App\Contracts\Events\DiscordMessageEvent;
use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Enums\Services\Discord\EmbedColor;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class ArtistSongDeleted.
 */
class ArtistSongDeleted extends ArtistSongEvent implements DiscordMessageEvent, UpdateRelatedIndicesEvent
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
        $song = $this->getSong();

        return DiscordMessage::create('', [
            'description' => "Song '**{$song->getName()}**' has been detached from Artist '**{$artist->getName()}**'.",
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

    /**
     * Perform updates on related indices.
     *
     * @return void
     */
    public function updateRelatedIndices()
    {
        // refresh artist document
        $artist = $this->getArtist();
        $artist->searchable();
    }
}
