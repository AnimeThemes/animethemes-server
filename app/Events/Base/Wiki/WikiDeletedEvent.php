<?php

declare(strict_types=1);

namespace App\Events\Base\Wiki;

use App\Constants\Config\ServiceConstants;
use App\Contracts\Events\NovaNotificationEvent;
use App\Events\Base\BaseDeletedEvent;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Laravel\Nova\Notifications\NovaNotification;

/**
 * Class WikiDeletedEvent.
 *
 * @template TModel of \App\Models\BaseModel
 * @extends BaseDeletedEvent<TModel>
 */
abstract class WikiDeletedEvent extends BaseDeletedEvent implements NovaNotificationEvent
{
    /**
     * Get Discord channel the message will be sent to.
     *
     * @return string
     */
    public function getDiscordChannel(): string
    {
        return Config::get(ServiceConstants::DB_UPDATES_DISCORD_CHANNEL_QUALIFIED);
    }

    /**
     * Determine if the notifications should be sent.
     *
     * @return bool
     */
    public function shouldSend(): bool
    {
        $model = $this->getModel();

        return ! $model->isForceDeleting();
    }

    /**
     * Get the nova notification.
     *
     * @return NovaNotification
     */
    public function getNotification(): NovaNotification
    {
        return NovaNotification::make()
            ->icon('flag')
            ->message($this->getNotificationMessage())
            ->type(NovaNotification::INFO_TYPE)
            ->url($this->getNotificationUrl());
    }

    /**
     * Get the users to notify.
     *
     * @return Collection
     */
    public function getUsers(): Collection
    {
        return User::query()
            ->whereRelation(User::RELATION_ROLES, Role::ATTRIBUTE_NAME, 'Admin')
            ->get();
    }

    /**
     * Get the message for the nova notification.
     *
     * @return string
     */
    abstract protected function getNotificationMessage(): string;

    /**
     * Get the URL for the nova notification.
     *
     * @return string
     */
    abstract protected function getNotificationUrl(): string;
}
