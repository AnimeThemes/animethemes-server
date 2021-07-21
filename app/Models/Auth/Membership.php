<?php

declare(strict_types=1);

namespace App\Models\Auth;

use Carbon\Carbon;
use Laravel\Jetstream\Membership as JetstreamMembership;

/**
 * Class Membership.
 *
 * @property int $id
 * @property int $team_id
 * @property int $user_id
 * @property string|null $role
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Membership extends JetstreamMembership
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d\TH:i:s.u';
}
