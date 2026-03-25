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
    public function shouldSendDiscordMessage(): bool
    {
        return false;
    }
}
