<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Models\Auth\User;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\HandlesAuthorization;
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

    public function before(User $user, string $ability): ?bool
    {
        if ($user->can(SpecialPermission::BYPASS_AUTHORIZATION->value)) {
            return true;
        }

        return null;
    }

    public function viewAny(?User $user): bool
    {
        if (Filament::isServing()) {
            return $user !== null && $user->can(CrudPermission::VIEW->format(static::getModel()));
        }

        return true;
    }

    public function view(?User $user, Model $model): bool
    {
        if (Filament::isServing()) {
            return $user !== null && $user->can(CrudPermission::VIEW->format(static::getModel()));
        }

        return true;
    }

    public function create(User $user): bool
    {
        return $user->can(CrudPermission::CREATE->format(static::getModel()));
    }

    public function update(User $user, Model $model): bool
    {
        $trashed = method_exists($model, 'trashed')
            ? $model->trashed()
            : false;

        return ! $trashed && $user->can(CrudPermission::UPDATE->format(static::getModel()));
    }

    public function updateAny(User $user): bool
    {
        return $user->can(CrudPermission::UPDATE->format(static::getModel()));
    }

    public function delete(User $user, Model $model): bool
    {
        $trashed = method_exists($model, 'trashed')
            ? $model->trashed()
            : false;

        return ! $trashed && $user->can(CrudPermission::DELETE->format(static::getModel()));
    }

    public function deleteAny(User $user): bool
    {
        return $user->can(CrudPermission::DELETE->format(static::getModel()));
    }

    public function forceDelete(User $user): bool
    {
        return $user->can(ExtendedCrudPermission::FORCE_DELETE->format(static::getModel()));
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can(ExtendedCrudPermission::FORCE_DELETE->format(static::getModel()));
    }

    public function restore(User $user, Model $model): bool
    {
        $trashed = method_exists($model, 'trashed')
            ? $model->trashed()
            : false;

        return $trashed && $user->can(ExtendedCrudPermission::RESTORE->format(static::getModel()));
    }

    public function restoreAny(User $user): bool
    {
        return $user->can(ExtendedCrudPermission::RESTORE->format(static::getModel()));
    }
}
