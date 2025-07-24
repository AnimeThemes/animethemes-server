<?php

declare(strict_types=1);

namespace App\Policies\User;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\User\Notification;
use App\Policies\BasePolicy;
use Illuminate\Database\Eloquent\Model;

class NotificationPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return $user?->can(CrudPermission::VIEW->format(Notification::class));
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  Notification  $notification
     */
    public function view(?User $user, Model $notification): bool
    {
        return $notification->notifiable()->is($user) && $user->can(CrudPermission::VIEW->format(Notification::class));
    }

    /**
     * Determine whether the user can mark the notification as read.
     *
     * @param  Notification  $notification
     */
    public function read(User $user, Model $notification): bool
    {
        return $notification->notifiable()->is($user) && $user->can(CrudPermission::UPDATE->format(Notification::class));
    }

    /**
     * Determine whether the user can mark the notification as unread.
     *
     * @param  Notification  $notification
     */
    public function unread(User $user, Model $notification): bool
    {
        return $notification->notifiable()->is($user) && $user->can(CrudPermission::UPDATE->format(Notification::class));
    }

    /**
     * Determine whether the user can mark the all their notifications as read.
     */
    public function readall(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(Notification::class));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  Notification  $notification
     */
    public function update(User $user, Model $notification): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  Notification  $notification
     */
    public function delete(User $user, Model $notification): bool
    {
        return false;
    }
}
