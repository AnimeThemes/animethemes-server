<?php

declare(strict_types=1);

namespace App\Events\List\Playlist;

use App\Events\Base\List\ListDeletedEvent;
use App\Models\List\Playlist;

/**
 * @extends ListDeletedEvent<Playlist>
 */
class PlaylistDeleted extends ListDeletedEvent
{
    public function __construct(Playlist $playlist)
    {
        parent::__construct($playlist);
    }

    public function shouldSendDiscordMessage(): bool
    {
        return false;
    }

    public function getModel(): Playlist
    {
        return $this->model;
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Playlist '**{$this->getModel()->getName()}**' has been deleted.";
    }
}
