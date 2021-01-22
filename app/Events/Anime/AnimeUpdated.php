<?php

namespace App\Events\Anime;

use App\Discord\Events\DiscordMessageEvent;
use App\Discord\Traits\HasAttributeUpdateEmbedFields;
use App\Scout\Events\UpdateRelatedIndicesEvent;
use App\Models\Anime;
use Illuminate\Foundation\Events\Dispatchable;
use NotificationChannels\Discord\DiscordMessage;

class AnimeUpdated extends AnimeEvent implements DiscordMessageEvent, UpdateRelatedIndicesEvent
{
    use Dispatchable, HasAttributeUpdateEmbedFields;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Anime $anime
     * @return void
     */
    public function __construct(Anime $anime)
    {
        parent::__construct($anime);
        $this->initializeEmbedFields($anime);
    }

    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        $anime = $this->getAnime();

        // TODO: messages shouldn't be hard-coded
        return DiscordMessage::create('Anime Updated', [
            'description' => "Anime '{$anime->name}' has been updated.",
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
        $anime = $this->getAnime();

        $anime->themes->each(function ($theme) {
            $theme->searchable();
            $theme->entries->each(function ($entry) {
                $entry->searchable();
                $entry->videos->searchable();
            });
        });
    }
}
