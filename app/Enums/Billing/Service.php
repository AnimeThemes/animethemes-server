<?php declare(strict_types=1);

namespace App\Enums\Billing;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * @method static static OTHER()
 * @method static static DIGITALOCEAN()
 * @method static static AWS()
 * @method static static HOVER()
 * @method static static WALKERSERVERS()
 */
final class Service extends Enum implements LocalizedEnum
{
    public const OTHER = 0;
    public const DIGITALOCEAN = 1;
    public const AWS = 2;
    public const HOVER = 3;
    public const WALKERSERVERS = 4;
}
