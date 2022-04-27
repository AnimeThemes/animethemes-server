<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime\Theme\Entry;

use App\Contracts\Events\DiscordMessageEvent;
use App\Contracts\Events\NovaNotificationEvent;
use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Enums\Services\Discord\EmbedColor;
use App\Models\Auth\User;
use App\Models\Wiki\Video;
use App\Nova\Resources\Wiki\Anime\Theme\Entry as EntryResource;
use App\Services\Nova\NovaQueries;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Laravel\Nova\Notifications\NovaNotification;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class EntryDeleted.
 */
class EntryDeleted extends EntryEvent implements DiscordMessageEvent, NovaNotificationEvent, UpdateRelatedIndicesEvent
{
    use Dispatchable;

    /**
     * Get Discord message payload.
     *
     * @return DiscordMessage
     */
    public function getDiscordMessage(): DiscordMessage
    {
        $entry = $this->getEntry();

        return DiscordMessage::create('', [
            'description' => "Entry '**{$entry->getName()}**' has been deleted.",
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
     * Perform updates on related indices.
     *
     * @return void
     */
    public function updateRelatedIndices(): void
    {
        $entry = $this->getEntry();

        $videos = $entry->videos;
        $videos->each(fn (Video $video) => $video->searchable());
    }

    /**
     * Determine if the notifications should be sent.
     *
     * @return bool
     */
    public function shouldSend(): bool
    {
        $entry = $this->getEntry();

        return ! $entry->isForceDeleting();
    }

    /**
     * Get the nova notification.
     *
     * @return NovaNotification
     */
    public function getNotification(): NovaNotification
    {
        $entry = $this->getEntry();

        $uriKey = EntryResource::uriKey();

        return NovaNotification::make()
            ->icon('flag')
            ->message("Entry '{$entry->getName()}' has been deleted. It will be automatically pruned in one week. Please review.")
            ->type(NovaNotification::INFO_TYPE)
            ->url("/resources/$uriKey/{$entry->getKey()}");
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
