<?php

declare(strict_types=1);

namespace App\Models\Auth;

use Carbon\Carbon;
use Laravel\Jetstream\Membership as JetstreamMembership;

/**
 * Class Membership.
 *
 * @property Carbon $created_at
 * @property int $id
 * @property string|null $role
 * @property int $team_id
 * @property Carbon $updated_at
 * @property int $user_id
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
