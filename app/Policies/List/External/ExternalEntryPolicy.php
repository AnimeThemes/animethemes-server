<?php

declare(strict_types=1);

namespace App\Policies\List\External;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\ExtendedCrudPermission;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\Models\Auth\User;
use App\Models\BaseModel;
use App\Models\List\ExternalProfile;
use App\Models\List\External\ExternalEntry;
use App\Policies\BasePolicy;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ExternalEntryPolicy.
 */
class ExternalEntryPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any models.
     *
     * @param  User|null  $user
     * @return bool
     */
    public function viewAny(?User $user): bool
    {
        if (Filament::isServing()) {
            return $user !== null && $user->hasRole('Admin');
        }

        /** @var ExternalProfile|null $profile */
        $profile = request()->route('externalprofile');

        return $user !== null
            ? ($user->getKey() === $profile?->user_id || ExternalProfileVisibility::PRIVATE !== $profile?->visibility) && $user->can(CrudPermission::VIEW->format(ExternalEntry::class))
            : ExternalProfileVisibility::PRIVATE !== $profile?->visibility;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User|null  $user
     * @param  ExternalEntry  $entry
     * @return bool
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function view(?User $user, BaseModel|Model $entry): bool
    {
        if (Filament::isServing()) {
            return $user !== null && $user->hasRole('Admin');
        }

        /** @var ExternalProfile|null $profile */
        $profile = request()->route('externalprofile');

        return $user !== null
            ? ($user->getKey() === $profile?->user_id || ExternalProfileVisibility::PRIVATE !== $profile?->visibility) && $user->can(CrudPermission::VIEW->format(ExternalEntry::class))
            : ExternalProfileVisibility::PRIVATE !== $profile?->visibility;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        if (Filament::isServing()) {
            return $user !== null && $user->hasRole('Admin');
        }

        /** @var ExternalProfile|null $profile */
        $profile = request()->route('externalprofile');

        return $user->getKey() === $profile?->user_id;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  ExternalEntry  $entry
     * @return bool
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function update(User $user, BaseModel|Model $entry): bool
    {
        if (Filament::isServing()) {
            return $user !== null && $user->hasRole('Admin');
        }

        /** @var ExternalProfile|null $profile */
        $profile = request()->route('externalprofile');

        return !$entry->trashed() && $user->getKey() === $profile?->user_id && $user->can(CrudPermission::UPDATE->format(ExternalEntry::class));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  ExternalEntry  $entry
     * @return bool
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function delete(User $user, BaseModel|Model $entry): bool
    {
        if (Filament::isServing()) {
            return $user !== null && $user->hasRole('Admin');
        }

        /** @var ExternalProfile|null $profile */
        $profile = request()->route('externalprofile');

        return !$entry->trashed() && $user->getKey() === $profile?->user_id && $user->can(CrudPermission::DELETE->format(ExternalEntry::class));
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  ExternalEntry  $entry
     * @return bool
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function restore(User $user, BaseModel|Model $entry): bool
    {
        if (Filament::isServing()) {
            return $user !== null && $user->hasRole('Admin');
        }

        /** @var ExternalProfile|null $profile */
        $profile = request()->route('externalprofile');

        return $entry->trashed() && $user->getKey() === $profile?->user_id && $user->can(ExtendedCrudPermission::RESTORE->format(ExternalEntry::class));
    }
}
