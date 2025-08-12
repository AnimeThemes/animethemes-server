<?php

declare(strict_types=1);

namespace App\GraphQL\Policies;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Models\Auth\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class BasePolicy.
 *
 * GraphQL will read any attach{model}, attachAny{model}, detach{model}, detachAny{model}
 * to make the validation for pivots. {model} must be the name of the model.
 */
abstract class BasePolicy
{
    use HandlesAuthorization;

    /**
     * Get the model class of the policy.
     *
     * @return class-string<Model>
     */
    protected static function getModel(): string
    {
        return Str::of(get_called_class())
            ->replace('GraphQL\\Policies', 'Models')
            ->remove('Policy')
            ->__toString();
    }

    /**
     * Perform pre-authorization checks.
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
     * @param  array  $args
     */
    public function viewAny(?User $user, array $args = []): bool
    {
        return $user === null || $user->can(CrudPermission::VIEW->format(static::getModel()));
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  array  $args
     */
    public function view(?User $user, array $args = [], ?string $keyName = 'model'): bool
    {
        return $user === null || $user->can(CrudPermission::VIEW->format(static::getModel()));
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  array  $args
     */
    public function create(User $user, array $args = []): bool
    {
        return $user->can(CrudPermission::CREATE->format(static::getModel()));
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  array  $args
     */
    public function update(User $user, array $args, ?string $keyName = 'model'): bool
    {
        /** @var Model $model */
        $model = Arr::get($args, $keyName);

        $trashed = method_exists($model, 'trashed')
            ? $model->trashed()
            : false;

        return ! $trashed && $user->can(CrudPermission::UPDATE->format(static::getModel()));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  array  $args
     */
    public function delete(User $user, array $args, ?string $keyName = 'model'): bool
    {
        /** @var Model $model */
        $model = Arr::get($args, $keyName);

        $trashed = method_exists($model, 'trashed')
            ? $model->trashed()
            : false;

        return ! $trashed && $user->can(CrudPermission::DELETE->format(static::getModel()));
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user): bool
    {
        return $user->can(ExtendedCrudPermission::FORCE_DELETE->format(static::getModel()));
    }

    /**
     * Determine whether the user can permanently delete any model.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can(ExtendedCrudPermission::FORCE_DELETE->format(static::getModel()));
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  array  $args
     */
    public function restore(User $user, array $args, ?string $keyName = 'model'): bool
    {
        /** @var Model $model */
        $model = Arr::get($args, $keyName);

        $trashed = method_exists($model, 'trashed')
            ? $model->trashed()
            : false;

        return $trashed && $user->can(ExtendedCrudPermission::RESTORE->format(static::getModel()));
    }

    /**
     * Determine whether the user can restore any model.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can(ExtendedCrudPermission::RESTORE->format(static::getModel()));
    }
}
