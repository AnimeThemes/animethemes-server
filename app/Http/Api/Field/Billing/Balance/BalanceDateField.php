<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Billing\Balance;

use App\Http\Api\Field\DateField;
use App\Models\Billing\Balance;

/**
 * Class BalanceDateField.
 */
class BalanceDateField extends DateField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Balance::ATTRIBUTE_DATE);
    }
}
