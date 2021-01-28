<?php

namespace App\Events\Theme;

use App\Discord\Events\DiscordMessageEvent;
use App\Discord\Traits\HasAttributeUpdateEmbedFields;
use App\Models\Entry;
use App\Models\Theme;
use App\Scout\Events\UpdateRelatedIndicesEvent;
use Illuminate\Foundation\Events\Dispatchable;
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
