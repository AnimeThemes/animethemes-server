<?php

declare(strict_types=1);

namespace App\Models\Auth;

use Carbon\Carbon;
use Laravel\Jetstream\TeamInvitation as JetstreamTeamInvitation;

/**
 *
 * Class TeamInvitation.
 *
 * @property int $id
 * @property int $team_id
 * @property string $email
 * @property string|null $role
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Team $team
 */
class TeamInvitation extends JetstreamTeamInvitation
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'team_invitations';

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d\TH:i:s.u';
}
