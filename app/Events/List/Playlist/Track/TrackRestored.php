<?php

declare(strict_types=1);

namespace App\Events\List\Playlist\Track;

use App\Constants\Config\ServiceConstants;
use App\Events\Base\BaseRestoredEvent;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Support\Facades\Config;

/**
 * Class TrackRestored.
 *
 * @extends BaseRestoredEvent<PlaylistTrack>
 */
class TrackRestored extends BaseRestoredEvent
{
    /**
     * The playlist the track belongs to.
     *
     * @var Playlist
     */
    protected Playlist $playlist;

    /**
     * Create a new event instance.
     *
     * @param  PlaylistTrack  $track
     */
    public function __construct(PlaylistTrack $track)
    {
        parent::__construct($track);
        $this->playlist = $track->playlist;
    }

    /**
     * Get Discord channel the message will be sent to.
     *
     * @return string
     */
    public function getDiscordChannel(): string
    {
        return Config::get(ServiceConstants::ADMIN_DISCORD_CHANNEL_QUALIFIED);
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
     * @return PlaylistTrack
     */
    public function getModel(): PlaylistTrack
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
        return "Track '**{$this->getModel()->getName()}**' has been restored for Playlist '**{$this->playlist->getName()}**'.";
    }
}
