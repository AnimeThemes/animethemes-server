<?php

declare(strict_types=1);

namespace App\Events\List\Playlist\Track;

use App\Events\Base\List\ListDeletedEvent;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Support\Str;

/**
 * @extends ListDeletedEvent<PlaylistTrack>
 */
class TrackDeleted extends ListDeletedEvent
{
    public function shouldSendDiscordMessage(): bool
    {
        return false;
    }

    protected function getDiscordMessageDescription(): string
    {
        $playlist = $this->getModel()->playlist;

        return Str::of("Track '**{$this->getModel()->getName()}**' has been deleted")
            ->append($playlist instanceof Playlist ? " for Playlist '**{$playlist->getName()}**'." : '.')
            ->__toString();
    }
}
