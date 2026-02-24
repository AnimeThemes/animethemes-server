<?php

declare(strict_types=1);

namespace App\Policies\Document;

use App\Enums\Auth\Role;
use App\Models\Auth\User;
use App\Models\Document\Page;
use App\Policies\BasePolicy;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

class PagePolicy extends BasePolicy
{
    /**
     * @param  Page  $page
     */
    public function view(?User $user, Model $page): Response
    {
        if ($user?->hasRole(Role::ADMIN->value) || $page->isPublic()) {
            return parent::view($user, $page);
        }

        /** @phpstan-ignore-next-line */
        return parent::view($user, $page) && $user?->hasAnyRole($page->roles)
            ? Response::allow()
            : Response::deny();
    }

    /**
     * @param  Page  $page
     */
    public function update(User $user, Model $page): Response
    {
        if ($user->hasRole(Role::ADMIN->value) || $page->editorRoles()->doesntExist()) {
            return parent::update($user, $page);
        }

        /** @phpstan-ignore-next-line */
        return parent::update($user, $page) && $user->hasAnyRole($page->editorRoles)
            ? Response::allow()
            : Response::deny();
    }

    /**
     * @param  Page  $page
     */
    public function delete(User $user, Model $page): Response
    {
        if ($user->hasRole(Role::ADMIN->value) || $page->editorRoles()->doesntExist()) {
            return parent::delete($user, $page);
        }

        /** @phpstan-ignore-next-line */
        return parent::delete($user, $page) && $user->hasAnyRole($page->editorRoles)
            ? Response::allow()
            : Response::deny();
    }

    /**
     * @param  Page  $page
     */
    public function forceDelete(User $user, ?Model $page = null): Response
    {
        if ($user->hasRole(Role::ADMIN->value) || $page->editorRoles()->doesntExist()) {
            return parent::forceDelete($user);
        }

        /** @phpstan-ignore-next-line */
        return parent::forceDelete($user) && $user->hasAnyRole($page->editorRoles)
            ? Response::allow()
            : Response::deny();
    }

    /**
     * @param  Page  $page
     */
    public function restore(User $user, Model $page): Response
    {
        if ($user->hasRole(Role::ADMIN->value) || $page->editorRoles()->doesntExist()) {
            return parent::restore($user, $page);
        }

        /** @phpstan-ignore-next-line */
        return parent::restore($user, $page) && $user->hasAnyRole($page->editorRoles)
            ? Response::allow()
            : Response::deny();
    }

    public function attachAnyRole(User $user): Response
    {
        return $user->hasRole(Role::ADMIN->value)
            ? Response::allow()
            : Response::deny();
    }

    public function detachAnyRole(User $user): Response
    {
        return $user->hasRole(Role::ADMIN->value)
            ? Response::allow()
            : Response::deny();
    }
}
