<?php declare(strict_types=1);

namespace App\Events\Song;

use App\Concerns\Discord\HasAttributeUpdateEmbedFields;
use App\Contracts\Events\DiscordMessageEvent;
use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Enums\Discord\EmbedColor;
use App\Models\Artist;
use App\Models\Entry;
use App\Models\Song;
use App\Models\Theme;
use App\Models\Video;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class SongUpdated
 * @package App\Events\Song
 */
class SongUpdated extends SongEvent implements DiscordMessageEvent, UpdateRelatedIndicesEvent
{
    use Dispatchable;
    use HasAttributeUpdateEmbedFields;

    /**
     * Create a new event instance.
     *
     * @param Song $song
     * @return void
     */
    public function __construct(Song $song)
    {
        parent::__construct($song);
        $this->initializeEmbedFields($song);
    }

    /**
     * Get Discord message payload.
     *
     * @return DiscordMessage
     */
    public function getDiscordMessage(): DiscordMessage
    {
        $song = $this->getSong();

        return DiscordMessage::create('', [
            'description' => "Song '**{$song->getName()}**' has been updated.",
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

    /**
     * Perform updates on related indices.
     *
     * @return void
     */
    public function updateRelatedIndices()
    {
        $song = $this->getSong()->load(['artists', 'themes.entries.videos']);

        $song->artists->each(function (Artist $artist) {
            $artist->searchable();
        });

        $song->themes->each(function (Theme $theme) {
            $theme->searchable();
            $theme->entries->each(function (Entry $entry) {
                $entry->searchable();
                $entry->videos->each(function (Video $video) {
                    $video->searchable();
                });
            });
        });
    }
}
