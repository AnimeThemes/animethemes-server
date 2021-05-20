<?php

namespace App\Events\Synonym;

use App\Contracts\Events\DiscordMessageEvent;
use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Enums\Discord\EmbedColor;
use App\Models\Entry;
use App\Models\Theme;
use App\Models\Video;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

class SynonymRestored extends SynonymEvent implements DiscordMessageEvent, UpdateRelatedIndicesEvent
{
    use Dispatchable, SerializesModels;

    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        $synonym = $this->getSynonym();
        $anime = $this->getAnime();

        return DiscordMessage::create('', [
            'description' => "Synonym '**{$synonym->getName()}**' has been restored for Anime '**{$anime->getName()}**'.",
            'color' => EmbedColor::GREEN,
        ]);
    }

    /**
     * Get Discord channel the message will be sent to.
     *
     * @return string
     */
    public function getDiscordChannel()
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
