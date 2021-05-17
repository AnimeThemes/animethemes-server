<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * @method static static OTHER()
 * @method static static DIGITALOCEAN()
 */
final class BillingService extends Enum implements LocalizedEnum
{
    const OTHER = 0;
    const DIGITALOCEAN = 1;
}
