<?php

declare(strict_types=1);

namespace App\Events\Base\Wiki;

use App\Constants\Config\ServiceConstants;
use App\Contracts\Events\FilamentNotificationEvent;
use App\Contracts\Events\NovaNotificationEvent;
use App\Enums\Auth\Role as RoleEnum;
use App\Events\Base\BaseDeletedEvent;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Laravel\Nova\Notifications\NovaNotification;

/**
 * Class WikiDeletedEvent.
 *
 * @template TModel of \App\Models\BaseModel
 *
 * @extends BaseDeletedEvent<TModel>
 */
abstract class WikiDeletedEvent extends BaseDeletedEvent implements NovaNotificationEvent, FilamentNotificationEvent
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
    public function shouldSendNovaNotification(): bool
    {
        $model = $this->getModel();

        return !$model->isForceDeleting();
    }

    /**
     * Get the nova notification.
     *
     * @return NovaNotification
     */
    public function getNovaNotification(): NovaNotification
    {
        return NovaNotification::make()
            ->icon('flag')
            ->message($this->getNotificationMessage())
            ->type(NovaNotification::INFO_TYPE)
            ->url($this->getNovaNotificationUrl());
    }

    /**
     * Get the users to notify.
     *
     * @return Collection
     */
    public function getNovaNotificationRecipients(): Collection
    {
        return User::query()
            ->whereRelation(User::RELATION_ROLES, Role::ATTRIBUTE_NAME, RoleEnum::ADMIN->value)
            ->get();
    }

    /**
     * Get the message for the nova/filament notification.
     *
     * @return string
     */
    abstract protected function getNotificationMessage(): string;

    /**
     * Get the URL for the nova notification.
     *
     * @return string
     */
    abstract protected function getNovaNotificationUrl(): string;

    /**
     * Determine if the notifications should be sent.
     *
     * @return bool
     */
    public function shouldSendFilamentNotification(): bool
    {
        $model = $this->getModel();

        return !$model->isForceDeleting();
    }

    /**
     * Get the nova notification.
     *
     * @return Notification
     */
    public function getFilamentNotification(): Notification
    {
        return Notification::make()
            ->body($this->getNotificationMessage())
            ->warning()
            ->actions([
                NotificationAction::make('view')
                    ->button()
                    ->url($this->getFilamentNotificationUrl()),

                NotificationAction::make('mark-as-read')
                    ->button()
                    ->markAsRead(),
            ]);
    }

    /**
     * Get the users to notify.
     *
     * @return Collection
     */
    public function getFilamentNotificationRecipients(): Collection
    {
        return User::query()
            ->whereRelation(User::RELATION_ROLES, Role::ATTRIBUTE_NAME, RoleEnum::ADMIN->value)
            ->get();
    }

    /**
     * Get the URL for the nova notification.
     *
     * @return string
     */
    abstract protected function getFilamentNotificationUrl(): string;
}
