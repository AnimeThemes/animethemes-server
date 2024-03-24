<?php

declare(strict_types=1);

namespace App\Events\List\Playlist;

use App\Constants\Config\ServiceConstants;
use App\Contracts\Events\AssignHashidsEvent;
use App\Events\Base\BaseCreatedEvent;
use App\Models\List\Playlist;
use Illuminate\Support\Facades\Config;

/**
 * Class PlaylistCreated.
 *
 * @extends BaseCreatedEvent<Playlist>
 */
class PlaylistCreated extends BaseCreatedEvent implements AssignHashidsEvent
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
        return true;
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
        return "Playlist '**{$this->getModel()->getName()}**' has been created.";
    }

    /**
     * Get the Hashids connection.
     *
     * @return string|null
     */
    public function getHashidsConnection(): ?string
    {
        return 'playlists';
    }
}
