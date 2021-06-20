<?php

declare(strict_types=1);

namespace App\Events\Wiki\Synonym;

use App\Concerns\Services\Discord\HasAttributeUpdateEmbedFields;
use App\Contracts\Events\DiscordMessageEvent;
use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Enums\Services\Discord\EmbedColor;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Synonym;
use App\Models\Wiki\Theme;
use App\Models\Wiki\Video;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class SynonymUpdated.
 */
class SynonymUpdated extends SynonymEvent implements DiscordMessageEvent, UpdateRelatedIndicesEvent
{
    use Dispatchable;
    use HasAttributeUpdateEmbedFields;

    /**
     * Create a new event instance.
     *
     * @param Synonym $synonym
     * @return void
     */
    public function __construct(Synonym $synonym)
    {
        parent::__construct($synonym);
        $this->initializeEmbedFields($synonym);
    }

    /**
     * Get Discord message payload.
     *
     * @return DiscordMessage
     */
    public function getDiscordMessage(): DiscordMessage
    {
        $synonym = $this->getSynonym();
        $anime = $this->getAnime();

        return DiscordMessage::create('', [
            'description' => "Synonym '**{$synonym->getName()}**' has been updated for Anime '**{$anime->getName()}**'.",
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
        $synonym = $this->getSynonym()->load('anime.themes.entries.videos');

        $synonym->anime->searchable();
        $synonym->anime->themes->each(function (Theme $theme) {
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
