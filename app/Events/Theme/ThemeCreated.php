<?php

namespace App\Events\Theme;

use App\Discord\Events\DiscordMessageEvent;
use App\Models\Entry;
use App\Scout\Events\UpdateRelatedIndicesEvent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
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
            'description' => "Theme '{$theme->slug}' has been created for Anime '{$anime->name}'.",
        ]);
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
            $entry->videos->searchable();
        });
    }
}
