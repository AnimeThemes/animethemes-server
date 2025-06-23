<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Models\Auth\User;
use App\Models\BaseModel;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Class BasePolicy.
 *
 * Filament and API will read any attach{model}, attachAny{model}, detach{model}, detachAny{model}
 * to make the validation for pivots. {model} must be the name of the model.
 */
abstract class BasePolicy
{
    use HandlesAuthorization;

    /**
     * Get the model class of the policy.
     *
     * @return class-string
     */
    protected static function getModel(): string
    {
        return Str::of(get_called_class())
            ->replace('Policies', 'Models')
            ->remove('Policy')
            ->__toString();
    }

    /**
     * Perform pre-authorization checks.
     *
     * @param  User  $user
     * @param  string  $ability
     * @return bool|null
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->can(SpecialPermission::BYPASS_AUTHORIZATION->value)) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  User|null  $user
     * @return bool
     */
    public function viewAny(?User $user): bool
    {
        if (Filament::isServing()) {
            return $user !== null && $user->can(CrudPermission::VIEW->format(static::getModel()));
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User|null  $user
     * @param  BaseModel|Model  $model
     * @return bool
     */
    public function view(?User $user, BaseModel|Model $model): bool
    {
        if (Filament::isServing()) {
            return $user !== null && $user->can(CrudPermission::VIEW->format(static::getModel()));
        }

        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can(CrudPermission::CREATE->format(static::getModel()));
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  BaseModel|Model  $model
     * @return bool
     */
    public function update(User $user, BaseModel|Model $model): bool
    {
        $trashed = method_exists($model, 'trashed')
            ? $model->trashed()
            : false;

        return (!$trashed && $user->can(CrudPermission::UPDATE->format(static::getModel())));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  BaseModel|Model  $model
     * @return bool
     */
    public function delete(User $user, BaseModel|Model $model): bool
    {
        $trashed = method_exists($model, 'trashed')
            ? $model->trashed()
            : false;

        return (!$trashed && $user->can(CrudPermission::DELETE->format(static::getModel())));
    }

    /**
     * Determine whether the user can delete any model.
     *
     * @param  User  $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        return $user->can(CrudPermission::DELETE->format(static::getModel()));
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @return bool
     */
    public function forceDelete(User $user): bool
    {
        return $user->can(ExtendedCrudPermission::FORCE_DELETE->format(static::getModel()));
    }

    /**
     * Determine whether the user can permanently delete any model.
     *
     * @param  User  $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can(ExtendedCrudPermission::FORCE_DELETE->format(static::getModel()));
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  BaseModel|Model  $model
     * @return bool
     */
    public function restore(User $user, BaseModel|Model $model): bool
    {
        $trashed = method_exists($model, 'trashed')
            ? $model->trashed()
            : false;

        return ($trashed && $user->can(ExtendedCrudPermission::RESTORE->format(static::getModel())));
    }

    /**
     * Determine whether the user can restore any model.
     *
     * @param  User  $user
     * @return bool
     */
    public function restoreAny(User $user): bool
    {
        return $user->can(ExtendedCrudPermission::RESTORE->format(static::getModel()));
    }
}
