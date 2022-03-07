<?php

declare(strict_types=1);

namespace App\Models\Auth;

use Carbon\Carbon;
use Laravel\Jetstream\TeamInvitation as JetstreamTeamInvitation;

/**
 * Class TeamInvitation.
 *
 * @property Carbon $created_at
 * @property string $email
 * @property int $id
 * @property string|null $role
 * @property Team $team
 * @property int $team_id
 * @property Carbon $updated_at
 */
class TeamInvitation extends JetstreamTeamInvitation
{
    final public const TABLE = 'team_invitations';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = TeamInvitation::TABLE;

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d\TH:i:s.u';
}
