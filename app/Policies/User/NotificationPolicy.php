<?php

declare(strict_types=1);

namespace App\Policies\User;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\User\Notification;
use App\Policies\BasePolicy;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

class NotificationPolicy extends BasePolicy
{
    public function viewAny(?User $user, mixed $value = null): Response
    {
        return $user?->can(CrudPermission::VIEW->format(Notification::class))
            ? Response::allow()
            : Response::deny();
    }

    /**
     * @param  Notification  $notification
     */
    public function view(?User $user, Model $notification): Response
    {
        return $notification->notifiable()->is($user) && $user->can(CrudPermission::VIEW->format(Notification::class))
            ? Response::allow()
            : Response::deny();
    }

    /**
     * @param  Notification  $notification
     */
    public function read(User $user, Model $notification): Response
    {
        return $notification->notifiable()->is($user) && $user->can(CrudPermission::UPDATE->format(Notification::class))
            ? Response::allow()
            : Response::deny();
    }

    /**
     * @param  Notification  $notification
     */
    public function unread(User $user, Model $notification): Response
    {
        return $notification->notifiable()->is($user) && $user->can(CrudPermission::UPDATE->format(Notification::class))
            ? Response::allow()
            : Response::deny();
    }

    public function readall(User $user): Response
    {
        return $user->can(CrudPermission::UPDATE->format(Notification::class))
            ? Response::allow()
            : Response::deny();
    }

    public function create(User $user): Response
    {
        return Response::deny();
    }

    /**
     * @param  Notification  $notification
     */
    public function update(User $user, Model $notification): Response
    {
        return Response::deny();
    }

    /**
     * @param  Notification  $notification
     */
    public function delete(User $user, Model $notification): Response
    {
        return Response::deny();
    }
}
