<?php

declare(strict_types=1);

namespace App\Models\Auth;

use Laravel\Jetstream\Membership as JetstreamMembership;

/**
 * Class Membership.
 */
class Membership extends JetstreamMembership
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;
}
