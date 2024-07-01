<?php

declare(strict_types=1);

namespace App\Events\List\Playlist;

use App\Events\Base\List\ListRestoredEvent;
use App\Models\List\Playlist;

/**
 * Class PlaylistRestored.
 *
 * @extends ListRestoredEvent<Playlist>
 */
class PlaylistRestored extends ListRestoredEvent
{
    /**
     * Create a new event instance.
     *
     * @param  Playlist  $playlist
     */
    public function __construct(Playlist $playlist)
    {
        parent::__construct($playlist);
    }

    /**
     * Determine if the message should be sent.
     *
     * @return bool
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function shouldSendDiscordMessage(): bool
    {
        return false;
    }

    /**
     * Get the model that has fired this event.
     *
     * @return Playlist
     */
    public function getModel(): Playlist
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     *
     * @return string
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Playlist '**{$this->getModel()->getName()}**' has been restored.";
    }
}
