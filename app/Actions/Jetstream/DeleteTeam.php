<?php

declare(strict_types=1);

namespace App\Actions\Jetstream;

use Laravel\Jetstream\Contracts\DeletesTeams;

/**
 * Class DeleteTeam
 * @package App\Actions\Jetstream
 */
class DeleteTeam implements DeletesTeams
{
    /**
     * Delete the given team.
     *
     * @param mixed $team
     * @return void
     */
    public function delete($team)
    {
        $team->purge();
    }
}
