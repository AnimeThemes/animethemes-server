<?php

namespace App\Events\Song;

use App\Contracts\Events\DiscordMessageEvent;
use App\Concerns\Discord\HasAttributeUpdateEmbedFields;
use App\Models\Artist;
use App\Models\Entry;
use App\Models\Song;
use App\Models\Theme;
use App\Models\Video;
use App\Contracts\Events\UpdateRelatedIndicesEvent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Config;
use NotificationChannels\Discord\DiscordMessage;

class SongUpdated extends SongEvent implements DiscordMessageEvent, UpdateRelatedIndicesEvent
{
    use Dispatchable, HasAttributeUpdateEmbedFields;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Song $song
     * @return void
     */
    public function __construct(Song $song)
    {
        parent::__construct($song);
        $this->initializeEmbedFields($song);
    }

    /**
     * Get Discord message payload.
     *
     * @return \NotificationChannels\Discord\DiscordMessage
     */
    public function getDiscordMessage()
    {
        $song = $this->getSong();

        // TODO: messages shouldn't be hard-coded
        return DiscordMessage::create('Song Updated', [
            'description' => "Song '{$song->getName()}' has been updated.",
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
        $song = $this->getSong();

        $song->artists->each(function (Artist $artist) {
            $artist->searchable();
        });

        $song->themes->each(function (Theme $theme) {
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
