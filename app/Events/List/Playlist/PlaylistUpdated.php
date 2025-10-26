<?php

declare(strict_types=1);

namespace App\Events\List\Playlist;

use App\Events\Base\List\ListUpdatedEvent;
use App\Models\List\Playlist;

/**
 * @extends ListUpdatedEvent<Playlist>
 */
class PlaylistUpdated extends ListUpdatedEvent
{
    public function __construct(Playlist $playlist)
    {
        parent::__construct($playlist);
        $this->initializeEmbedFields($playlist);
    }

    public function shouldSendDiscordMessage(): bool
    {
        return false;
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Playlist '**{$this->getModel()->getName()}**' has been updated.";
    }
}
