<?php

namespace App\Enums\Billing;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * @method static static OTHER()
 * @method static static DIGITALOCEAN()
 */
final class Service extends Enum implements LocalizedEnum
{
    const OTHER = 0;
    const DIGITALOCEAN = 1;
    const AWS = 2;
    const HOVER = 3;
    const WALKERSERVERS = 4;
}
