<?php

declare(strict_types=1);

namespace App\Actions\Jetstream;

use App\Models\Auth\Team;
use Illuminate\Support\Facades\DB;
use Laravel\Jetstream\Contracts\DeletesTeams;
use Laravel\Jetstream\Contracts\DeletesUsers;

/**
 * Class DeleteUser.
 */
class DeleteUser implements DeletesUsers
{
    /**
     * Create a new action instance.
     *
     * @param  DeletesTeams  $deletesTeams
     * @return void
     */
    public function __construct(protected DeletesTeams $deletesTeams)
    {
    }

    /**
     * Delete the given user.
     *
     * @param  mixed  $user
     * @return void
     */
    public function delete($user): void
    {
        DB::transaction(function () use ($user) {
            $this->deleteTeams($user);
            $user->tokens->each->delete();
            $user->delete();
        });
    }

    /**
     * Delete the teams and team associations attached to the user.
     *
     * @param  mixed  $user
     * @return void
     */
    protected function deleteTeams(mixed $user): void
    {
        $user->teams()->detach();

        $user->ownedTeams->each(function (Team $team) {
            $this->deletesTeams->delete($team);
        });
    }
}
