<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Billing\Balance;

use App\Enums\Models\Billing\Service;
use App\Http\Api\Field\EnumField;
use App\Models\Billing\Balance;

/**
 * Class BalanceServiceField.
 */
class BalanceServiceField extends EnumField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Balance::ATTRIBUTE_SERVICE, Service::class);
    }
}
