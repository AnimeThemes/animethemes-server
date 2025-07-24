<?php

declare(strict_types=1);

namespace App\Events\List\Playlist;

use App\Events\Base\List\ListDeletedEvent;
use App\Models\List\Playlist;

/**
 * Class PlaylistDeleted.
 *
 * @extends ListDeletedEvent<Playlist>
 */
class PlaylistDeleted extends ListDeletedEvent
{
    public function __construct(Playlist $playlist)
    {
        parent::__construct($playlist);
    }

    /**
     * Determine if the message should be sent.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function shouldSendDiscordMessage(): bool
    {
        return false;
    }

    /**
     * Get the model that has fired this event.
     */
    public function getModel(): Playlist
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Playlist '**{$this->getModel()->getName()}**' has been deleted.";
    }
}
