<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime\Theme\Entry;

use App\Concerns\Services\Discord\HasAttributeUpdateEmbedFields;
use App\Contracts\Events\DiscordMessageEvent;
use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Enums\Services\Discord\EmbedColor;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class EntryUpdated.
 */
class EntryUpdated extends EntryEvent implements DiscordMessageEvent, UpdateRelatedIndicesEvent
{
    use Dispatchable;
    use HasAttributeUpdateEmbedFields;

    /**
     * Create a new event instance.
     *
     * @param  AnimeThemeEntry  $entry
     * @return void
     */
    public function __construct(AnimeThemeEntry $entry)
    {
        parent::__construct($entry);
        $this->initializeEmbedFields($entry);
    }

    /**
     * Get Discord message payload.
     *
     * @return DiscordMessage
     */
    public function getDiscordMessage(): DiscordMessage
    {
        $entry = $this->getEntry();

        return DiscordMessage::create('', [
            'description' => "Entry '**{$entry->getName()}**' has been updated.",
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
        $entry = $this->getEntry();

        $entry->videos->each(fn (Video $video) => $video->searchable());
    }
}
