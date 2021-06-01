<?php declare(strict_types=1);

namespace App\Enums\Billing;

use BenSampo\Enum\Enum;

/**
 * Class Frequency
 * @package App\Enums\Billing
 */
final class Frequency extends Enum
{
    public const ONCE = 0;
    public const ANNUALLY = 1;
    public const BIANNUALLY = 2;
    public const QUARTERLY = 3;
    public const MONTHLY = 4;
}
