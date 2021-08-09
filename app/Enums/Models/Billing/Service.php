<?php

declare(strict_types=1);

namespace App\Enums\Models\Billing;

use App\Enums\BaseEnum;

/**
 * Class Service.
 *
 * @method static static OTHER()
 * @method static static DIGITALOCEAN()
 * @method static static AWS()
 * @method static static HOVER()
 * @method static static WALKERSERVERS()
 */
final class Service extends BaseEnum
{
    public const OTHER = 0;
    public const DIGITALOCEAN = 1;
    public const AWS = 2;
    public const HOVER = 3;
    public const WALKERSERVERS = 4;
}
