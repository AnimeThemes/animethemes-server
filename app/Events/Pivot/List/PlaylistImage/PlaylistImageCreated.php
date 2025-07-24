<?php

declare(strict_types=1);

namespace App\Events\Pivot\List\PlaylistImage;

use App\Constants\Config\ServiceConstants;
use App\Events\Base\Pivot\PivotCreatedEvent;
use App\Models\List\Playlist;
use App\Models\Wiki\Image;
use App\Pivots\List\PlaylistImage;
use Illuminate\Support\Facades\Config;

/**
 * Class PlaylistImageCreated.
 *
 * @extends PivotCreatedEvent<Playlist, Image>
 */
class PlaylistImageCreated extends PivotCreatedEvent
{
    public function __construct(PlaylistImage $playlistImage)
    {
        parent::__construct($playlistImage->playlist, $playlistImage->image);
    }

    /**
     * Get Discord channel the message will be sent to.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getDiscordChannel(): string
    {
        return Config::get(ServiceConstants::ADMIN_DISCORD_CHANNEL_QUALIFIED);
    }

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        $foreign = $this->getForeign();
        $related = $this->getRelated();

        return "Image '**{$foreign->getName()}**' has been attached to Playlist '**{$related->getName()}**'.";
    }
}
