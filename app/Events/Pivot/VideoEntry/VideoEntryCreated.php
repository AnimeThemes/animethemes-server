<?php declare(strict_types=1);

namespace App\Events\Pivot\VideoEntry;

use App\Contracts\Events\DiscordMessageEvent;
use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Enums\Discord\EmbedColor;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class VideoEntryCreated
 * @package App\Events\Pivot\VideoEntry
 */
class VideoEntryCreated extends VideoEntryEvent implements DiscordMessageEvent, UpdateRelatedIndicesEvent
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
        $video = $this->getVideo();
        $entry = $this->getEntry();

        return DiscordMessage::create('', [
            'description' => "Video '**{$video->getName()}**' has been attached to Entry '**{$entry->getName()}**'.",
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

    /**
     * Perform updates on related indices.
     *
     * @return void
     */
    public function updateRelatedIndices()
    {
        // refresh video document
        $video = $this->getVideo();
        $video->searchable();
    }
}
