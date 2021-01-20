<?php

namespace App\Events\Anime;

use App\Models\Anime;
use App\Events\DiscordMessageEvent;
use App\Events\HasDiscordEmbedFields;
use Illuminate\Foundation\Events\Dispatchable;
use NotificationChannels\Discord\DiscordMessage;

class AnimeUpdated extends AnimeEvent implements DiscordMessageEvent
{
    use Dispatchable, HasDiscordEmbedFields;

    /**
     * The array of embed fields.
     *
     * @var array
     */
    protected $embedFields;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Anime $anime
     * @return void
     */
    public function __construct(Anime $anime)
    {
        parent::__construct($anime);
        $this->embedFields = static::initializeEmbedFields($anime);
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
            'fields' => $this->embedFields,
        ]);
    }
}
