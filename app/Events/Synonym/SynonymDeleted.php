<?php

namespace App\Events\Synonym;

use App\Discord\Events\DiscordMessageEvent;
use App\Models\Entry;
use App\Models\Theme;
use App\Scout\Events\UpdateRelatedIndicesEvent;
use Illuminate\Foundation\Events\Dispatchable;
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
            'description' => "Synonym '{$synonym->text}' has been deleted for Anime '{$anime->name}'.",
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

        $anime->searchable();
        $anime->themes->each(function (Theme $theme) {
            $theme->searchable();
            $theme->entries->each(function (Entry $entry) {
                $entry->searchable();
                $entry->videos->searchable();
            });
        });
    }
}
