<?php

declare(strict_types=1);

namespace App\Events\Base\Wiki;

use App\Constants\Config\ServiceConstants;
use App\Contracts\Events\FilamentNotificationEvent;
use App\Enums\Auth\Role as RoleEnum;
use App\Events\Base\BaseDeletedEvent;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

/**
 * Class WikiDeletedEvent.
 *
 * @template TModel of \App\Models\BaseModel
 *
 * @extends BaseDeletedEvent<TModel>
 */
abstract class WikiDeletedEvent extends BaseDeletedEvent implements FilamentNotificationEvent
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
     * Get the message for the filament notification.
     *
     * @return string
     */
    abstract protected function getNotificationMessage(): string;

    /**
     * Determine if the notifications should be sent.
     *
     * @return bool
     */
    public function shouldSendFilamentNotification(): bool
    {
        $model = $this->getModel();

        return ! $model->isForceDeleting();
    }

    /**
     * Get the filament notification.
     *
     * @return Notification
     */
    public function getFilamentNotification(): Notification
    {
        return Notification::make()
            ->body($this->getNotificationMessage())
            ->warning()
            ->actions([
                Action::make('view')
                    ->button()
                    ->url($this->getFilamentNotificationUrl()),

                Action::make('mark-as-read')
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
     * Get the URL for the filament notification.
     *
     * @return string
     */
    abstract protected function getFilamentNotificationUrl(): string;
}
