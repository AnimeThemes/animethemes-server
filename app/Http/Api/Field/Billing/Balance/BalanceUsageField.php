<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Billing\Balance;

use App\Http\Api\Field\FloatField;
use App\Models\Billing\Balance;

/**
 * Class BalanceUsageField.
 */
class BalanceUsageField extends FloatField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Balance::ATTRIBUTE_USAGE);
    }
}
