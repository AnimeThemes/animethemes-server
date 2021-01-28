<?php

namespace App\Events\Synonym;

use App\Discord\Events\DiscordMessageEvent;
use App\Discord\Traits\HasAttributeUpdateEmbedFields;
use App\Models\Entry;
use App\Models\Synonym;
use App\Models\Theme;
use App\Scout\Events\UpdateRelatedIndicesEvent;
use Illuminate\Foundation\Events\Dispatchable;
use NotificationChannels\Discord\DiscordMessage;

class SynonymUpdated extends SynonymEvent implements DiscordMessageEvent, UpdateRelatedIndicesEvent
{
    use Dispatchable, HasAttributeUpdateEmbedFields;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Synonym $synonym
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
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        $synonym = $this->getSynonym();
        $anime = $this->getAnime();

        // TODO: messages shouldn't be hard-coded
        return DiscordMessage::create('Synonym Updated', [
            'description' => "Synonym '{$synonym->getName()}' has been updated for Anime '{$anime->getName()}'.",
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
        $synonym = $this->getSynonym();

        $synonym->anime->searchable();
        $synonym->anime->themes->each(function (Theme $theme) {
            $theme->searchable();
            $theme->entries->each(function (Entry $entry) {
                $entry->searchable();
                $entry->videos->searchable();
            });
        });
    }
}
