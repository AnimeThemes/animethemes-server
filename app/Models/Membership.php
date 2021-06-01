<?php

declare(strict_types=1);

namespace App\Models;

use Laravel\Jetstream\Membership as JetstreamMembership;

/**
 * Class Membership
 * @package App\Models
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
