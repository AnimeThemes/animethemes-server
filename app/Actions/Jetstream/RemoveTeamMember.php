<?php

declare(strict_types=1);

namespace App\Actions\Jetstream;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Laravel\Jetstream\Contracts\RemovesTeamMembers;
use Laravel\Jetstream\Events\TeamMemberRemoved;

/**
 * Class RemoveTeamMember.
 */
class RemoveTeamMember implements RemovesTeamMembers
{
    /**
     * Remove the team member from the given team.
     *
     * @param  mixed  $user
     * @param  mixed  $team
     * @param  mixed  $teamMember
     * @return void
     *
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function remove(mixed $user, mixed $team, mixed $teamMember)
    {
        $this->authorize($user, $team, $teamMember);

        $this->ensureUserDoesNotOwnTeam($teamMember, $team);

        $team->removeUser($teamMember);

        TeamMemberRemoved::dispatch($team, $teamMember);
    }

    /**
     * Authorize that the user can remove the team member.
     *
     * @param  mixed  $user
     * @param  mixed  $team
     * @param  mixed  $teamMember
     * @return void
     *
     * @throws AuthorizationException
     */
    protected function authorize(mixed $user, mixed $team, mixed $teamMember)
    {
        if (
            ! Gate::forUser($user)->check('removeTeamMember', $team)
            && $user->id !== $teamMember->id
        ) {
            throw new AuthorizationException();
        }
    }

    /**
     * Ensure that the currently authenticated user does not own the team.
     *
     * @param  mixed  $teamMember
     * @param  mixed  $team
     * @return void
     *
     * @throws ValidationException
     */
    protected function ensureUserDoesNotOwnTeam(mixed $teamMember, mixed $team)
    {
        if ($teamMember->id === $team->owner->id) {
            throw ValidationException::withMessages([
                'team' => [__('You may not leave a team that you created.')],
            ])->errorBag('removeTeamMember');
        }
    }
}
