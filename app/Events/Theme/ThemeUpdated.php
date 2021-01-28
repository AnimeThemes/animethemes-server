<?php

namespace App\Events\Theme;

use App\Contracts\Events\DiscordMessageEvent;
use App\Concerns\Discord\HasAttributeUpdateEmbedFields;
use App\Models\Entry;
use App\Models\Theme;
use App\Models\Video;
use App\Contracts\Events\UpdateRelatedIndicesEvent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

class ThemeUpdated extends ThemeEvent implements DiscordMessageEvent, UpdateRelatedIndicesEvent
{
    use Dispatchable, HasAttributeUpdateEmbedFields;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Theme $theme
     * @return void
     */
    public function __construct(Theme $theme)
    {
        parent::__construct($theme);
        $this->initializeEmbedFields($theme);
    }

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
        return DiscordMessage::create('Theme Updated', [
            'description' => "Theme '{$theme->getName()}' has been updated for Anime '{$anime->getName()}'.",
            'fields' => $this->getEmbedFields(),
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
