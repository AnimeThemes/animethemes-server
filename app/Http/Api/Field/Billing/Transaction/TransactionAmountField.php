<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Billing\Transaction;

use App\Http\Api\Field\FloatField;
use App\Models\Billing\Transaction;

/**
 * Class TransactionAmountField.
 */
class TransactionAmountField extends FloatField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Transaction::ATTRIBUTE_AMOUNT);
    }
}
