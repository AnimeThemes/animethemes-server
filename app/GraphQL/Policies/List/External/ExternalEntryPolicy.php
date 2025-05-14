<?php

declare(strict_types=1);

namespace App\GraphQL\Policies\List\External;

use App\Enums\Models\List\ExternalProfileVisibility;
use App\GraphQL\Policies\BasePolicy;
use App\Models\Auth\User;
use App\Models\List\ExternalProfile;
use Illuminate\Support\Arr;

/**
 * Class ExternalEntryPolicy.
 */
class ExternalEntryPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any models.
     *
     * @param  User|null  $user
     * @param  array|null  $injected
     * @return bool
     */
    public function viewAny(?User $user, ?array $injected = null): bool
    {
        /** @var ExternalProfile|null $profile */
        $profile = Arr::get($injected, 'profile');

        return $user !== null
            ? ($profile?->user()->is($user) || ExternalProfileVisibility::PRIVATE !== $profile?->visibility) && parent::viewAny($user, $injected)
            : ExternalProfileVisibility::PRIVATE !== $profile?->visibility;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User|null  $user
     * @param  array|null  $injected
     * @param  string|null  $keyName
     * @return bool
     */
    public function view(?User $user, ?array $injected = null, ?string $keyName = 'id'): bool
    {
        /** @var ExternalProfile|null $profile */
        $profile = Arr::get($injected, 'profile');

        return $user !== null
            ? ($profile?->user()->is($user) || ExternalProfileVisibility::PRIVATE !== $profile?->visibility) && parent::view($user, $injected, $keyName)
            : ExternalProfileVisibility::PRIVATE !== $profile?->visibility;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @param  array|null  $injected
     * @return bool
     */
    public function create(User $user, ?array $injected = null): bool
    {
        /** @var ExternalProfile|null $profile */
        $profile = Arr::get($injected, 'profile');

        return $profile?->user()->is($user) && parent::create($user, $injected);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  array  $injected
     * @param  string|null  $keyName
     * @return bool
     */
    public function update(User $user, array $injected, ?string $keyName = 'id'): bool
    {
        /** @var ExternalProfile|null $profile */
        $profile = Arr::get($injected, 'profile');

        return $profile?->user()->is($user) && parent::update($user, $injected, $keyName);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  array  $injected
     * @param  string|null  $keyName
     * @return bool
     */
    public function delete(User $user, array $injected, ?string $keyName = 'id'): bool
    {
        /** @var ExternalProfile|null $profile */
        $profile = Arr::get($injected, 'profile');

        return $profile?->user()->is($user) && parent::delete($user, $injected, $keyName);
    }
}
