<?php

declare(strict_types=1);

namespace App\Policies\Auth;

use App\Models\Auth\Team;
use App\Models\Auth\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Config;

/**
 * Class TeamPolicy.
 */
class TeamPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @return bool
     */
    public function viewAny(): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Team $team
     * @return bool
     */
    public function view(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        $teamCreator = User::find(Config::get('jetstream.creator'));

        return $teamCreator !== null && $user->is($teamCreator);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Team $team
     * @return bool
     */
    public function update(User $user, Team $team): bool
    {
        return $user->ownsTeam($team);
    }

    /**
     * Determine whether the user can add team members.
     *
     * @param User $user
     * @param Team $team
     * @return bool
     */
    public function addTeamMember(User $user, Team $team): bool
    {
        return $user->ownsTeam($team);
    }

    /**
     * Determine whether the user can update team member permissions.
     *
     * @param User $user
     * @param Team $team
     * @return bool
     */
    public function updateTeamMember(User $user, Team $team): bool
    {
        return $user->ownsTeam($team);
    }

    /**
     * Determine whether the user can remove team members.
     *
     * @param User $user
     * @param Team $team
     * @return bool
     */
    public function removeTeamMember(User $user, Team $team): bool
    {
        return $user->ownsTeam($team);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Team $team
     * @return bool
     */
    public function delete(User $user, Team $team): bool
    {
        return $user->ownsTeam($team);
    }
}
