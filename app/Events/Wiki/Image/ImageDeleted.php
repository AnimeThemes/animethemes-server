<?php

declare(strict_types=1);

namespace App\Events\Wiki\Image;

use App\Contracts\Events\DiscordMessageEvent;
use App\Contracts\Events\NovaNotificationEvent;
use App\Enums\Services\Discord\EmbedColor;
use App\Models\Auth\User;
use App\Nova\Resources\Wiki\Image as ImageResource;
use App\Services\Nova\NovaQueries;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Laravel\Nova\Notifications\NovaNotification;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class ImageDeleted.
 */
class ImageDeleted extends ImageEvent implements DiscordMessageEvent, NovaNotificationEvent
{
    use Dispatchable;

    /**
     * Get Discord message payload.
     *
     * @return DiscordMessage
     */
    public function getDiscordMessage(): DiscordMessage
    {
        $image = $this->getImage();

        return DiscordMessage::create('', [
            'description' => "Image '**{$image->getName()}**' has been deleted.",
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
        $image = $this->getImage();

        return ! $image->isForceDeleting();
    }

    /**
     * Get the nova notification.
     *
     * @return NovaNotification
     */
    public function getNotification(): NovaNotification
    {
        $image = $this->getImage();

        $uriKey = ImageResource::uriKey();

        return NovaNotification::make()
            ->icon('flag')
            ->message("Image '{$image->getName()}' has been deleted. It will be automatically pruned in one week. Please review.")
            ->type(NovaNotification::INFO_TYPE)
            ->url("/resources/$uriKey/{$image->getKey()}");
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
