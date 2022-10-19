<?php

declare(strict_types=1);

namespace App\Events\Pivot\List\PlaylistImage;

use App\Constants\Config\ServiceConstants;
use App\Events\Base\Pivot\PivotDeletedEvent;
use App\Models\List\Playlist;
use App\Models\Wiki\Image;
use App\Pivots\List\PlaylistImage;
use Illuminate\Support\Facades\Config;

/**
 * Class PlaylistImageDeleted.
 *
 * @extends PivotDeletedEvent<Playlist, Image>
 */
class PlaylistImageDeleted extends PivotDeletedEvent
{
    /**
     * Create a new event instance.
     *
     * @param  PlaylistImage  $playlistImage
     */
    public function __construct(PlaylistImage $playlistImage)
    {
        parent::__construct($playlistImage->playlist, $playlistImage->image);
    }

    /**
     * Get Discord channel the message will be sent to.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getDiscordChannel(): string
    {
        return Config::get(ServiceConstants::ADMIN_DISCORD_CHANNEL_QUALIFIED);
    }

    /**
     * Get the description for the Discord message payload.
     *
     * @return string
     */
    protected function getDiscordMessageDescription(): string
    {
        $foreign = $this->getForeign();
        $related = $this->getRelated();

        return "Image '**{$foreign->getName()}**' has been detached from Playlist '**{$related->getName()}**'.";
    }
}
