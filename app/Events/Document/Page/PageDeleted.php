<?php

declare(strict_types=1);

namespace App\Events\Document\Page;

use App\Contracts\Events\DiscordMessageEvent;
use App\Contracts\Events\NovaNotificationEvent;
use App\Enums\Services\Discord\EmbedColor;
use App\Models\Auth\User;
use App\Nova\Resources\Document\Page as PageResource;
use App\Services\Nova\NovaQueries;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Laravel\Nova\Notifications\NovaNotification;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class PageDeleted.
 */
class PageDeleted extends PageEvent implements DiscordMessageEvent, NovaNotificationEvent
{
    use Dispatchable;

    /**
     * Get Discord message payload.
     *
     * @return DiscordMessage
     */
    public function getDiscordMessage(): DiscordMessage
    {
        $page = $this->getPage();

        return DiscordMessage::create('', [
            'description' => "Page '**{$page->getName()}**' has been deleted.",
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
        $page = $this->getPage();

        return ! $page->isForceDeleting();
    }

    /**
     * Get the nova notification.
     *
     * @return NovaNotification
     */
    public function getNotification(): NovaNotification
    {
        $page = $this->getPage();

        $uriKey = PageResource::uriKey();

        return NovaNotification::make()
            ->icon('flag')
            ->message("Page '{$page->getName()}' has been deleted. It will be automatically pruned in one week. Please review.")
            ->type(NovaNotification::INFO_TYPE)
            ->url("/resources/$uriKey/{$page->getKey()}");
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
