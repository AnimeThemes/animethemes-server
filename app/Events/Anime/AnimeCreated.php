<?php

namespace App\Events\Anime;

use App\Discord\Events\DiscordMessageEvent;
use App\Scout\Events\UpdateRelatedIndicesEvent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NotificationChannels\Discord\DiscordMessage;

class AnimeCreated extends AnimeEvent implements DiscordMessageEvent, UpdateRelatedIndicesEvent
{
    use Dispatchable, SerializesModels;

    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        $anime = $this->getAnime();

        // TODO: messages shouldn't be hard-coded
        return DiscordMessage::create('Anime Created', [
            'description' => "Anime '{$anime->name}' has been created.",
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
