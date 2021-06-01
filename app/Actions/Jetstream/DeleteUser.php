<?php declare(strict_types=1);

namespace App\Actions\Jetstream;

use App\Models\Team;
use Illuminate\Support\Facades\DB;
use Laravel\Jetstream\Contracts\DeletesTeams;
use Laravel\Jetstream\Contracts\DeletesUsers;

/**
 * Class DeleteUser
 * @package App\Actions\Jetstream
 */
class DeleteUser implements DeletesUsers
{
    /**
     * The team deleter implementation.
     *
     * @var DeletesTeams
     */
    protected DeletesTeams $deletesTeams;

    /**
     * Create a new action instance.
     *
     * @param DeletesTeams $deletesTeams
     * @return void
     */
    public function __construct(DeletesTeams $deletesTeams)
    {
        $this->deletesTeams = $deletesTeams;
    }

    /**
     * Delete the given user.
     *
     * @param mixed $user
     * @return void
     */
    public function delete($user)
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
     * @param mixed $user
     * @return void
     */
    protected function deleteTeams(mixed $user)
    {
        $user->teams()->detach();

        $user->ownedTeams->each(function (Team $team) {
            $this->deletesTeams->delete($team);
        });
    }
}
