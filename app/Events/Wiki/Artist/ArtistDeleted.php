<?php

declare(strict_types=1);

namespace App\Events\Wiki\Artist;

use App\Contracts\Events\DiscordMessageEvent;
use App\Contracts\Events\NovaNotificationEvent;
use App\Enums\Services\Discord\EmbedColor;
use App\Models\Auth\User;
use App\Nova\Resources\Wiki\Artist as ArtistResource;
use App\Services\Nova\NovaQueries;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Laravel\Nova\Notifications\NovaNotification;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class ArtistDeleted.
 */
class ArtistDeleted extends ArtistEvent implements DiscordMessageEvent, NovaNotificationEvent
{
    use Dispatchable;

    /**
     * Get Discord message payload.
     *
     * @return DiscordMessage
     */
    public function getDiscordMessage(): DiscordMessage
    {
        $artist = $this->getArtist();

        return DiscordMessage::create('', [
            'description' => "Artist '**{$artist->getName()}**' has been deleted.",
            'color' => EmbedColor::RED,
        ]);
    }

    /**
     * Get Discord channel the message will be sent to.
     *
     * @return string
     */
    public function getDiscordChannel(): string
    {
        return Config::get('services.discord.db_updates_discord_channel');
    }

    /**
     * Determine if the notifications should be sent.
     *
     * @return bool
     */
    public function shouldSend(): bool
    {
        $artist = $this->getArtist();

        return ! $artist->isForceDeleting();
    }

    /**
     * Get the nova notification.
     *
     * @return NovaNotification
     */
    public function getNotification(): NovaNotification
    {
        $artist = $this->getArtist();

        $uriKey = ArtistResource::uriKey();

        return NovaNotification::make()
            ->icon('flag')
            ->message("Artist '{$artist->getName()}' has been deleted. It will be automatically pruned in one week. Please review.")
            ->type(NovaNotification::INFO_TYPE)
            ->url("/resources/$uriKey/{$artist->getKey()}");
    }

    /**
     * Get the users to notify.
     *
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return NovaQueries::admins();
    }
}
