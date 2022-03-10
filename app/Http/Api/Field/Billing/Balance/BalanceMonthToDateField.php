<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Billing\Balance;

use App\Http\Api\Field\FloatField;
use App\Http\Resources\Billing\Resource\BalanceResource;
use App\Models\Billing\Balance;

/**
 * Class BalanceMonthToDateField.
 */
class BalanceMonthToDateField extends FloatField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(BalanceResource::ATTRIBUTE_BALANCE, Balance::ATTRIBUTE_BALANCE);
    }
}
