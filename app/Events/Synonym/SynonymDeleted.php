<?php

namespace App\Events\Synonym;

use App\Contracts\Events\DiscordMessageEvent;
use App\Models\Entry;
use App\Models\Theme;
use App\Models\Video;
use App\Contracts\Events\UpdateRelatedIndicesEvent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

class SynonymDeleted extends SynonymEvent implements DiscordMessageEvent, UpdateRelatedIndicesEvent
{
    use Dispatchable;

    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        $synonym = $this->getSynonym();
        $anime = $this->getAnime();

        // TODO: messages shouldn't be hard-coded
        return DiscordMessage::create('Synonym Deleted', [
            'description' => "Synonym '{$synonym->getName()}' has been deleted for Anime '{$anime->getName()}'.",
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
        $anime = $this->getAnime();

        $anime->searchable();
        $anime->themes->each(function (Theme $theme) {
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
