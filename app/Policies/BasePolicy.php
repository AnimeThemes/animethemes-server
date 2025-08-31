<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Models\Auth\User;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Filament and API will read any attach{model}, attachAny{model}, detach{model}, detachAny{model}
 * to make the validation for pivots. {model} must be the name of the model.
 */
abstract class BasePolicy
{
    use HandlesAuthorization;

    /**
     * @return class-string<Model>
     */
    protected static function getModel(): string
    {
        return Str::of(get_called_class())
            ->replace('Policies', 'Models')
            ->remove('Policy')
            ->__toString();
    }

    public function before(User $user, string $ability): ?Response
    {
        if ($user->can(SpecialPermission::BYPASS_AUTHORIZATION->value)) {
            return Response::allow();
        }

        return null;
    }

    public function viewAny(?User $user): Response
    {
        if (Filament::isServing()) {
            return $user !== null && $user->can(CrudPermission::VIEW->format(static::getModel()))
                ? Response::allow()
                : Response::deny();
        }

        return Response::allow();
    }

    public function view(?User $user, Model $model): Response
    {
        if (Filament::isServing()) {
            return $user !== null && $user->can(CrudPermission::VIEW->format(static::getModel()))
                ? Response::allow()
                : Response::deny();
        }

        return Response::allow();
    }

    public function create(User $user): Response
    {
        return $user->can(CrudPermission::CREATE->format(static::getModel()))
            ? Response::allow()
            : Response::deny();
    }

    public function update(User $user, Model $model): Response
    {
        $trashed = method_exists($model, 'trashed')
            ? $model->trashed()
            : false;

        return ! $trashed && $user->can(CrudPermission::UPDATE->format(static::getModel()))
            ? Response::allow()
            : Response::deny();
    }

    public function updateAny(User $user): Response
    {
        return $user->can(CrudPermission::UPDATE->format(static::getModel()))
            ? Response::allow()
            : Response::deny();
    }

    public function delete(User $user, Model $model): Response
    {
        $trashed = method_exists($model, 'trashed')
            ? $model->trashed()
            : false;

        return ! $trashed && $user->can(CrudPermission::DELETE->format(static::getModel()))
            ? Response::allow()
            : Response::deny();
    }

    public function deleteAny(User $user): Response
    {
        return $user->can(CrudPermission::DELETE->format(static::getModel()))
            ? Response::allow()
            : Response::deny();
    }

    public function forceDelete(User $user): Response
    {
        return $user->can(ExtendedCrudPermission::FORCE_DELETE->format(static::getModel()))
            ? Response::allow()
            : Response::deny();
    }

    public function forceDeleteAny(User $user): Response
    {
        return $user->can(ExtendedCrudPermission::FORCE_DELETE->format(static::getModel()))
            ? Response::allow()
            : Response::deny();
    }

    public function restore(User $user, Model $model): Response
    {
        $trashed = method_exists($model, 'trashed')
            ? $model->trashed()
            : false;

        return $trashed && $user->can(ExtendedCrudPermission::RESTORE->format(static::getModel()))
            ? Response::allow()
            : Response::deny();
    }

    public function restoreAny(User $user): Response
    {
        return $user->can(ExtendedCrudPermission::RESTORE->format(static::getModel()))
            ? Response::allow()
            : Response::deny();
    }
}
