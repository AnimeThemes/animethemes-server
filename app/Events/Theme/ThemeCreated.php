<?php

namespace App\Events\Theme;

use App\Contracts\Events\DiscordMessageEvent;
use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Models\Entry;
use App\Models\Video;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

class ThemeCreated extends ThemeEvent implements DiscordMessageEvent, UpdateRelatedIndicesEvent
{
    use Dispatchable, SerializesModels;

    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        $theme = $this->getTheme();
        $anime = $this->getAnime();

        // TODO: messages shouldn't be hard-coded
        return DiscordMessage::create('Theme Created', [
            'description' => "Theme '{$theme->getName()}' has been created for Anime '{$anime->getName()}'.",
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
        $theme = $this->getTheme();

        $theme->entries->each(function (Entry $entry) {
            $entry->searchable();
            $entry->videos->each(function (Video $video) {
                $video->searchable();
            });
        });
    }
}
