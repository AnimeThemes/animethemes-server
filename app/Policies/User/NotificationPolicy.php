<?php

declare(strict_types=1);

namespace App\Policies\User;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\BaseModel;
use App\Models\User\Notification;
use App\Policies\BasePolicy;
use Illuminate\Database\Eloquent\Model;

/**
 * Class NotificationPolicy.
 */
class NotificationPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any models.
     *
     * @param  User|null  $user
     * @return bool
     */
    public function viewAny(?User $user): bool
    {
        return $user?->can(CrudPermission::VIEW->format(Notification::class));
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User|null  $user
     * @param  Notification  $notification
     * @return bool
     */
    public function view(?User $user, BaseModel|Model $notification): bool
    {
        return $notification->notifiable()->is($user) && $user->can(CrudPermission::VIEW->format(Notification::class));
    }

    /**
     * Determine whether the user can mark the notification as read.
     *
     * @param  User  $user
     * @param  Notification  $notification
     * @return bool
     */
    public function read(User $user, BaseModel|Model $notification): bool
    {
        return $notification->notifiable()->is($user) && $user->can(CrudPermission::UPDATE->format(Notification::class));
    }

    /**
     * Determine whether the user can mark the notification as unread.
     *
     * @param  User  $user
     * @param  Notification  $notification
     * @return bool
     */
    public function unread(User $user, BaseModel|Model $notification): bool
    {
        return $notification->notifiable()->is($user) && $user->can(CrudPermission::UPDATE->format(Notification::class));
    }

    /**
     * Determine whether the user can mark the all their notifications as read.
     *
     * @param  User  $user
     * @return bool
     */
    public function readall(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Notification::class));
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Notification  $notification
     * @return bool
     */
    public function update(User $user, BaseModel|Model $notification): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Notification  $notification
     * @return bool
     */
    public function delete(User $user, BaseModel|Model $notification): bool
    {
        return false;
    }
}
